<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * 
 * @property int $id
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $patronymic
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $password
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
class User extends Model
{
	protected $table = 'users';

	protected $casts = [
		'role_id' => 'int'
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
