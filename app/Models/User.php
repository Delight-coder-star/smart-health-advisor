<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'student_id',
        'phone',
        'department',
        'role',
        'password',
        'avatar',
        'date_of_birth',
        'gender',
        'is_active',
        'risk_score',
        'risk_level',
        'last_assessment_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_assessment_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'risk_score' => 'integer',
    ];

    // -------------------- Relationships --------------------

    public function assessmentSessions()
    {
        return $this->hasMany(AssessmentSession::class);
    }

    public function assessmentReports()
    {
        return $this->hasMany(AssessmentReport::class);
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function assignedAlerts()
    {
        return $this->hasMany(Alert::class, 'assigned_to');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function latestAssessment()
    {
        return $this->hasOne(Assessment::class)->latestOfMany();
    }

    public function videoAnalyses()
    {
        return $this->hasMany(VideoAnalysis::class);
    }

    // -------------------- Scopes --------------------

    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    public function scopeAdvisors($query)
    {
        return $query->where('role', 'advisor');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeAtRisk($query)
    {
        return $query->whereIn('risk_level', ['high', 'critical']);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // -------------------- Accessors --------------------

    public function getRiskColorAttribute(): string
    {
        return match ($this->risk_level) {
            'low' => 'success',
            'moderate' => 'warning',
            'high' => 'orange',
            'critical' => 'danger',
            default => 'secondary',
        };
    }

    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim($this->name ?? ''));
        $initials = '';
        foreach ($parts as $p) {
            if ($p !== '') {
                $initials .= strtoupper(substr($p, 0, 1));
            }
        }
        return substr($initials, 0, 2) ?: 'U';
    }

    // -------------------- Role Helpers --------------------

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAdvisor(): bool
    {
        return $this->role === 'advisor';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }
}