<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Patient
 * 
 * @property int $id
 * @property int|null $user_id
 * @property string|null $address
 * @property string|null $gender
 * @property string|null $allergies
 * @property string|null $chronic_diseases
 * @property Carbon|null $birth_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User|null $user
 * @property Collection|Appointment[] $appointments
 *
 * @package App\Models
 */
class Patient extends Model
{
	protected $table = 'patients';

	protected $casts = [
		'user_id' => 'int',
		'birth_date' => 'datetime'
	];

	protected $fillable = [
		'user_id',
		'address',
		'gender',
		'allergies',
		'chronic_diseases',
		'birth_date'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function appointments()
	{
		return $this->hasMany(Appointment::class);
	}
}
