<?php

namespace App\Models;

use App\Notifications\ApiResetPassword;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements \Illuminate\Contracts\Auth\MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, MustVerifyEmail;

	protected $table = 'users';

	protected $casts = [
		'role_id' => 'int',
        'password' => 'hashed',
	];

	protected $hidden = [
		'password'
	];

    protected $fillable = [
        'firstname',
        'lastname',
        'patronymic',
        'phone',
        'email',
        'password',
        'avatar',
        'role_id',
        'email_verified_at'
    ];

	public function role()
	{
		return $this->belongsTo(Role::class);
	}

	public function doctor()
	{
		return $this->hasOne(Doctor::class);
	}

	public function notifications()
	{
		return $this->hasMany(Notification::class);
	}

	public function patient()
	{
		return $this->hasOne(Patient::class);
	}

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ApiResetPassword($token));
    }
}
