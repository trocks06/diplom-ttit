<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
