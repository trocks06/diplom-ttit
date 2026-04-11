<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
