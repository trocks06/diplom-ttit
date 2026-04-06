<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Notification
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $text
 * @property bool|null $is_read
 * @property Carbon|null $created_at
 *
 * @property User|null $user
 *
 * @package App\Models
 */
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
