<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Spatie Permission Trait
use Spatie\Permission\Traits\HasRoles;

// Filament Access Management Trait (Üçüncü parti, varsa doğru namespace kullandığına emin ol)
use SolutionForest\FilamentAccessManagement\Concerns\FilamentUserHelpers;

use App\Models\Expense;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, FilamentUserHelpers;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Kullanıcı ile giderler arasındaki ilişki
    public function giderler()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Filament paneline erişim kontrolü.
     * Sadece admin rolüne sahip kullanıcılar erişebilir.
     */
    public function canAccessPanel(): bool
    {
        return $this->hasRole('admin');
    }
}
