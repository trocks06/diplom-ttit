<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'notes',
        'file_name',
        'file_type',
        'file_path'
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
