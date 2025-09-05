<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;
    
    protected $table = 'appointments';
    protected $primaryKey = 'appointment_id';
    protected $fillable = ['user_id', 'doctor_name', 'appointment_date', 'location', 'notes', 'status'];
    
    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    /**
     * Get the user that owns the appointment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope to get upcoming appointments
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now())
                    ->where('status', '!=', 'cancelled');
    }

    /**
     * Scope to get appointments for today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', today());
    }

    /**
     * Scope to get appointments for tomorrow
     */
    public function scopeTomorrow($query)
    {
        return $query->whereDate('appointment_date', now()->addDay());
    }
}