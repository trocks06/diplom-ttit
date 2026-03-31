<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Review
 * 
 * @property int $id
 * @property int|null $appointment_id
 * @property int|null $rating
 * @property string|null $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Appointment|null $appointment
 *
 * @package App\Models
 */
class Review extends Model
{
	protected $table = 'reviews';

	protected $casts = [
		'appointment_id' => 'int',
		'rating' => 'int'
	];

	protected $fillable = [
		'appointment_id',
		'rating',
		'comment'
	];

	public function appointment()
	{
		return $this->belongsTo(Appointment::class);
	}
}
