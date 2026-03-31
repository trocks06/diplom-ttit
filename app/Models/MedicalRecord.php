<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MedicalRecord
 * 
 * @property int $id
 * @property int|null $appointment_id
 * @property string|null $diagnosis
 * @property string|null $treatment
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Appointment|null $appointment
 * @property Collection|MedicalFile[] $medical_files
 *
 * @package App\Models
 */
class MedicalRecord extends Model
{
	protected $table = 'medical_records';

	protected $casts = [
		'appointment_id' => 'int'
	];

	protected $fillable = [
		'appointment_id',
		'diagnosis',
		'treatment',
		'notes'
	];

	public function appointment()
	{
		return $this->belongsTo(Appointment::class);
	}

	public function medical_files()
	{
		return $this->hasMany(MedicalFile::class);
	}
}
