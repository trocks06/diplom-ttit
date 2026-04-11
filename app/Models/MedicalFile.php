<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalFile extends Model
{
	protected $table = 'medical_files';
	public $timestamps = true;
    const UPDATED_AT = null;

	protected $casts = [
		'medical_record_id' => 'int'
	];

	protected $fillable = [
		'medical_record_id',
		'file_name',
		'file_path',
		'file_type'
	];

	public function medical_record()
	{
		return $this->belongsTo(MedicalRecord::class);
	}
}
