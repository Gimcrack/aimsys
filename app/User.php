<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];



    /**
     * A User may belong to many groups
     *
     * @return \belongsToMany
     */
    public function groups()
    {
      return $this->belongsToMany('App\Group')->withTimestamps();

    }


    /**
     * Is user an admin
     *
     * @return boolean
     */
    public function isAdmin()
    {
      return  ( $this->groups()->where('name','Administrators')->count() ) or
              ( $this->groups()->where('name','Super Administrators')->count() );

    }

    /**
     * Is user a super admin
     *
     * @return boolean [description]
     */
    public function isSuperAdmin()
    {
      return  ( $this->groups()->where('name','Super Administrators')->count() );
    }
}
