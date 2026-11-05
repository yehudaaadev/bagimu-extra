<?php

namespace App\Models;

use App\Enums\Role;
use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'username',
        'student_status',
        'wali_nama',
        'wali_kontak',
        'whatsapp',
        'notify_wa',
        'notify_email',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'login_token',
        'login_token_hash',
        'totp_secret_enc',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_set_at' => 'datetime',
            'role' => Role::class,
            'student_status' => StudentStatus::class,
            'totp_enabled' => 'boolean',
            'notify_wa' => 'boolean',
            'notify_email' => 'boolean',
            'login_token_created_at' => 'datetime',
            'login_token_revoked_at' => 'datetime',
        ];
    }

    public function setWhatsappAttribute($value): void
    {
        $this->attributes['whatsapp'] = $value ? \App\Support\Whatsapp::normalize($value) : null;
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function eskulDiampu(): HasMany
    {
        return $this->hasMany(Eskul::class, 'pembina_id');
    }

    public function inappNotifications(): HasMany
    {
        return $this->hasMany(InAppNotification::class);
    }

    public function loginHistory(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function classHistory(): HasMany
    {
        return $this->hasMany(ClassHistory::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function hasRole(Role ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMIN);
    }

    public function isStudent(): bool
    {
        return $this->hasRole(Role::STUDENT);
    }

    public function isWaliKelas(): bool
    {
        return $this->hasRole(Role::WALI_KELAS);
    }

    public function managedClass(): HasOne
    {
        return $this->hasOne(SchoolClass::class, 'wali_kelas_id');
    }

    /**
     * Normalisasi alamat email: otomatis tambahkan domain '@gmail.com' bila diisi tanpa '@'.
     */
    public static function normalizeEmail(?string $email): ?string
    {
        if ($email === null) {
            return null;
        }

        $email = trim(strtolower($email));
        if ($email === '') {
            return '';
        }

        if (! str_contains($email, '@')) {
            return $email.'@gmail.com';
        }

        if (str_ends_with($email, '@gmail')) {
            return $email.'.com';
        }

        return $email;
    }
}
