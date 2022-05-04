<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','mobile','user_fcm_token','app_version','first_name','last_name','dob','pincode','modeofbanking',
        'stay_type','modeofsalary','experience','type_of_employment','salary','existing_emi','salary_sms','loan_amt','tenure','purpose',
        'status','emi_type','address','state','rejection_status','work_email_id','gender','pan_card'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Please ADD this two methods at the end of the class
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function fullName()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function aggregators()
    {
        return $this->hasMany('App\Models\AggregatorsRequest');
    }
}
