<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 *
 * @property int $id
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $patronymic
 * @property string|null $phone
 * @property string $email
 * @property string $password
 * @property string|null $avatar
 * @property bool|null $verified
 * @property int|null $role_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Role|null $role
 * @property Doctor|null $doctor
 * @property Collection|Notification[] $notifications
 * @property Patient|null $patient
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

	protected $table = 'users';

	protected $casts = [
		'verified' => 'bool',
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
		'verified',
		'role_id'
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
}
