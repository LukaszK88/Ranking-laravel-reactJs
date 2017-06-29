<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventAttendCategory;
use App\Models\EventAttendence;
use App\Models\EventCategories;
use App\Models\EventType;
use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;

class EventsController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $events = Event::with('user')
            ->with('eventType')
            ->with('category')
           ->with('attendance')
           ->with(['note','note.user'])
            ->get();

        return $this->respond($events);

    }

    public function getEventsByType($type)
    {
        $events = Event::leftJoin(Image::TABLE, function ($join) {
            $join->on(Event::TCOL_ID,'=',Image::TCOL_EVENT_ID)
                ->where(Image::TCOL_IMAGE_TYPE_ID, '=', 1);})
            ->join(EventType::TABLE,EventType::TABLE.'.'.EventType::COL_ID,'=',Event::TCOL_EVENT_TYPE_ID)
            ->select('events.*',EventType::TCOL_TYPE, Image::TCOL_URL)
            ->where(Event::TCOL_EVENT_TYPE_ID,$type)
            //->groupBy(EventType::TCOL_ID)
            ->with('user')
            ->with('category')
            ->get();

        return $this->respond($events);
    }

    public function getEventTypes()
    {
        $types = EventType::all();

        return $this->respond($types);
    }

    public function getEventsAttendedByUser($userId, Event $event){

        $events = $event->getAttendingEvents($userId);

        return $this->respond($events);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $event = Event::create([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'body' => $data['body'],
            'location' => $data['location'],
            'date' => $data['date'],
            'event_type_id' => $data['event_type_id'],
        ]);


           foreach ($data['categories'] as $category => $key){

               EventCategories::create([
                   'event_id' => $event->id,
                   'name' => $category
               ]);
           }

        return $this->respond($event);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::where(Event::COL_ID,$id)
            ->with('user')
            ->with('eventType')
            ->with('category')
            ->with('attendance')
            ->first();

        return $this->respond($event);
    }

    public function getEventAttendees($eventId, EventAttendence $eventAttendence){

        $eventAttendees = $eventAttendence->getEventAttendees($eventId);

        return $this->respond($eventAttendees);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $event = Event::where(Event::COL_ID,$id);
        $data = $request->all();
        if($event){
            $event->update([
                'user_id' => $data['user_id'],
                'title' => $data['title'],
                'body' => $data['body'],
                'location' => $data['location'],
                'date' => $data['date'],
                'event_type_id' => $data['event_type_id'],
            ]);
//TODO delete on false
            foreach ($data['categories'] as $category => $key){

                EventCategories::updateOrCreate(['event_id' => $id,'name' => $category],[
                    'event_id' => $id,
                    'name' => $category
                ]);


            }
            return $this->respond($data);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Event::find($id)->delete();

        return $this->responseDeleted('Event Deleted');
    }

    public function attendEvent($eventId, $userId, Request $request){

        $attendance  =EventAttendence::updateOrCreate([
            EventAttendence::COL_EVENT_ID => $eventId,
            EventAttendence::COL_USER_ID => $userId
        ],[
            EventAttendence::COL_GOING => $request->input('going')
        ]);

        return $this->respond($attendance);
    }

    public function storeEventAttendedCategories($eventAttendId,Request $request){

        foreach ($request->all() as $category => $key){
            if($key === true) {
                EventAttendCategory::updateOrCreate([
                    EventAttendCategory::COL_EVENT_ATTEND_ID => $eventAttendId,
                    EventAttendCategory::COL_NAME => $category
                ]);
            }elseif ($key === false){
                EventAttendCategory::where(EventAttendCategory::COL_EVENT_ATTEND_ID,$eventAttendId)
                    ->where(EventAttendCategory::COL_NAME, $category)
                    ->delete();
            }
        }
        return $this->responseCreated('Categories attended');
    }

}