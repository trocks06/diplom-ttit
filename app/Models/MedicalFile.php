<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MedicalFile
 * 
 * @property int $id
 * @property int|null $medical_record_id
 * @property string|null $file_name
 * @property string|null $file_path
 * @property string|null $file_type
 * @property Carbon|null $created_at
 * 
 * @property MedicalRecord|null $medical_record
 *
 * @package App\Models
 */
class MedicalFile extends Model
{
	protected $table = 'medical_files';
	public $timestamps = false;

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
