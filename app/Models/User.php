<?php

namespace App\Models;
use App\Http\Middleware\TrimStrings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    protected $table = self::TABLE;

    const TABLE = 'users';

    const   COL_USER_ROLE_ID = 'user_role_id',
            COL_NAME = 'name',
            COL_TEMP_PASSWORD = 'temp_password',
            COL_STATUS = 'status',
            COL_AGE = 'age',
            COL_ABOUT = 'about',
            COL_QUOTE = 'quote',
            COL_PASSWORD = 'password',
            COL_FACEBOOK = 'facebook',
            COL_GOOGLE = 'google',
            COL_WEIGHT = 'weight',
            COL_TOTAL_POINTS = 'total_points',
            COL_FACEBOOK_IMG = 'facebook_picture',
            COL_GOOGLE_IMG = 'google_picture',
            COL_IMG = 'image',
            COL_USERNAME = 'username',
            COL_CLUB_ID = 'club_id',
            COL_CLUB_ADMIN_ID = 'club_admin_id',
            COL_LAST_LOGIN = 'last_login',
            COL_ID = 'id';

    const   TCOL_USER_ROLE_ID = self::TABLE.'.'.self::COL_USER_ROLE_ID,
            TCOL_NAME = self::TABLE.'.'.self::COL_NAME,
            TCOL_FACEBOOK_IMG = self::TABLE.'.'.self::COL_FACEBOOK_IMG,
            TCOL_GOOGLE_IMG = self::TABLE.'.'.self::COL_GOOGLE_IMG,
            TCOL_IMG = self::TABLE.'.'.self::COL_IMG,
            TCOL_USERNAME = self::TABLE.'.'.self::COL_USERNAME,
            TCOL_CLUB = self::TABLE.'.'.self::COL_CLUB_ID,
            TCOL_ID = self::TABLE.'.'.self::COL_ID;

    const REL_BOHURT = 'bohurt',
        REL_SWORD_SHIELD = 'swordShield',
        REL_PROFIGHT = 'profight',
        REL_SWORD_BUCKLER = 'swordBuckler',
        REL_LONGSWORD = 'longsword',
        REL_POLEARM = 'polearm',
        REL_TRIATHLON = 'triathlon',
        REL_EVENT = 'event',
        REL_CLUB = 'club';

    const RREL_BOHURT_EVENT = 'bohurt.event',
        RREL_SWORD_SHIELD_EVENT = 'swordShield.event',
        RREL_PROFIGHT_EVENT = 'profight.event',
        RREL_SWORD_BUCKLER_EVENT = 'swordBuckler.event',
        RREL_LONGSWORD_EVENT = 'longsword.event',
        RREL_POLEARM_EVENT = 'polearm.event',
        RREL_TRIATHLON_EVENT = 'triathlon.event',
        RREL_BOHURT_EVENT_ACHIEVEMENT = 'bohurt.eventAchievement',
        RREL_SWORD_SHIELD_EVENT_ACHIEVEMENT = 'swordShield.eventAchievement',
        RREL_PROFIGHT_EVENT_ACHIEVEMENT = 'profight.eventAchievement',
        RREL_SWORD_BUCKLER_EVENT_ACHIEVEMENT = 'swordBuckler.eventAchievement',
        RREL_LONGSWORD_EVENT_ACHIEVEMENT = 'longsword.eventAchievement',
        RREL_POLEARM_EVENT_ACHIEVEMENT = 'polearm.eventAchievement',
        RREL_TRIATHLON_EVENT_ACHIEVEMENT = 'triathlon.eventAchievement';

    protected $fillable=[
        self::COL_NAME,
        self::COL_USERNAME,
        self::COL_USER_ROLE_ID,
        self::COL_STATUS,
        self::COL_AGE,
        self::COL_ABOUT,
        self::COL_QUOTE,
        self::COL_PASSWORD,
        self::COL_TEMP_PASSWORD,
        self::COL_FACEBOOK,
        self::COL_GOOGLE,
        self::COL_FACEBOOK_IMG,
        self::COL_GOOGLE_IMG,
        self::COL_WEIGHT,
        self::COL_TOTAL_POINTS,
        self::COL_IMG,
        self::COL_CLUB_ID,
        self::COL_CLUB_ADMIN_ID,
        self::COL_LAST_LOGIN
    ];

    public function conversationsFrom()
    {
        return $this->hasMany(Conversation::class,'from','id');
    }

    public function conversationsTo()
    {
        return $this->hasMany(Conversation::class,'to','id');
    }

    public function getUserWithCategoriesHeAttendsOnEvent($eventAttendId,$userId)
    {
        return User::
            select(User::TCOL_ID, User::TCOL_USERNAME)
            ->where(self::TCOL_ID,$userId)
            ->with(['eventAttendCategory' => function ($query) use ($eventAttendId) {
                $query->where(EventAttendCategory::TCOL_EVENT_ATTEND_ID, '=', $eventAttendId);
            }])
            ->first();
    }

    public function note()
    {
        return $this->belongsTo(EventNote::class, 'user_id', 'id');
    }

    public function bohurt()
    {
        return $this->hasMany(Bohurt::class);
    }

    public function post()
    {
        return $this->hasMany(Post::class);
    }

    public function triathlon()
    {
        return $this->hasMany(Triathlon::class);
    }

    public function swordShield()
    {
        return $this->hasMany(SwordShield::class);
    }

    public function longsword()
    {
        return $this->hasMany(Longsword::class);
    }

    public function polearm()
    {
        return $this->hasMany(Polearm::class);
    }

    public function profight()
    {
        return $this->hasMany(Profight::class);
    }

    public function swordBuckler()
    {
        return $this->hasMany(SwordBuckler::class);
    }

    public function achievement()
    {
        return $this->hasMany(Achievement::class);
    }

    public function event()
    {
        return $this->hasMany(Event::class);
    }

    public function role()
    {
        return $this->hasOne(UserRole::class,'id','user_role_id');
    }

    public function eventAttendCategory()
    {
        return $this->hasManyThrough(
            EventAttendCategory::class,
            EventAttendence::class,
            'user_id', 'event_attend_id', 'id'
        );
    }

    public function attendence()
    {
        return $this->hasMany(EventAttendence::class,'user_id','id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class,'club_id','id');
    }
}
