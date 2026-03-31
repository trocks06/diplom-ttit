<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Status
 * 
 * @property int $id
 * @property string|null $status_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Appointment[] $appointments
 *
 * @package App\Models
 */
class Status extends Model
{
	protected $table = 'statuses';

	protected $fillable = [
		'status_name'
	];

	public function appointments()
	{
		return $this->hasMany(Appointment::class);
	}
}
