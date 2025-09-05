<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    use HasFactory;

    protected $table = 'medical_history';
    protected $primaryKey = 'history_id';
    protected $fillable = [
        'user_id',
        'condition_name',
        'description',
        'diagnosed_date',
        'resolved_date'
    ];
}
