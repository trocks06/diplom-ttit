<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Specialization
 * 
 * @property int $id
 * @property string $specialization_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Doctor[] $doctors
 *
 * @package App\Models
 */
class Specialization extends Model
{
	protected $table = 'specializations';

	protected $fillable = [
		'specialization_name'
	];

	public function doctors()
	{
		return $this->belongsToMany(Doctor::class, 'doctor_specializations');
	}
}
