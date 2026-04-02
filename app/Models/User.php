<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'birth_date',
        'avatar',
        'position',
        'bio',
        'notif_email',
        'notif_browser',
        'notif_project_updates',
        'notif_client_activity',
        'privacy_show_email',
        'privacy_show_phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date'        => 'date',
        'password'          => 'hashed',
        'notif_email'       => 'boolean',
        'notif_browser'     => 'boolean',
        'notif_project_updates' => 'boolean',
        'notif_client_activity' => 'boolean',
        'privacy_show_email' => 'boolean',
        'privacy_show_phone' => 'boolean',
    ];

    /**
     * Returns initials from name (up to 2 characters).
     */
    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', trim($this->name));
        $initials = '';
        foreach ($parts as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
            if (strlen($initials) >= 2) break;
        }
        return $initials ?: 'U';
    }

    /**
     * Returns the full avatar URL or null.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    public function functionalAreas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserFunctionalArea::class);
    }

    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }
}
