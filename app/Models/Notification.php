<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
	protected $table = 'notifications';
    const UPDATED_AT = null;

	protected $casts = [
		'user_id' => 'int',
		'is_read' => 'bool'
	];

	protected $fillable = [
		'user_id',
		'text',
		'is_read'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
