<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Doctor
 * 
 * @property int $id
 * @property int|null $user_id
 * @property int|null $specialization_id
 * @property string|null $license
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User|null $user
 * @property Specialization|null $specialization
 * @property Collection|Schedule[] $schedules
 *
 * @package App\Models
 */
class Doctor extends Model
{
	protected $table = 'doctors';

	protected $casts = [
		'user_id' => 'int',
		'specialization_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'specialization_id',
		'license'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function specialization()
	{
		return $this->belongsTo(Specialization::class);
	}

	public function schedules()
	{
		return $this->hasMany(Schedule::class);
	}
}
