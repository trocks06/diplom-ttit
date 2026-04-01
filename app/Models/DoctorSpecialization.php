<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DoctorSpecialization
 * 
 * @property int $doctor_id
 * @property int $specialization_id
 * 
 * @property Doctor $doctor
 * @property Specialization $specialization
 *
 * @package App\Models
 */
class DoctorSpecialization extends Model
{
	protected $table = 'doctor_specializations';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'doctor_id' => 'int',
		'specialization_id' => 'int'
	];

	public function doctor()
	{
		return $this->belongsTo(Doctor::class);
	}

	public function specialization()
	{
		return $this->belongsTo(Specialization::class);
	}
}
