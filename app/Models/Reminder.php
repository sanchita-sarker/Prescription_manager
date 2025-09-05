<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $primaryKey = 'reminder_id';   // custom PK
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'medicine_name',
        'reminder_time',
        'start_date',
        'end_date',
        'frequency',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}



