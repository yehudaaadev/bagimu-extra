<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Eskul extends Model
{
    protected $fillable = [
        'name',
        'description',
        'kuota',
        'kuota_aktif',
        'aktif',
        'pembina_id',
    ];

    protected function casts(): array
    {
        return [
            'kuota_aktif' => 'boolean',
            'aktif' => 'boolean',
        ];
    }

    public function pembina(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembina_id');
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(EskulJadwal::class);
    }

    public function pertemuans(): HasMany
    {
        return $this->hasMany(Pertemuan::class)->orderBy('nomor');
    }

    public function kehadiran(): HasMany
    {
        return $this->hasMany(Kehadiran::class);
    }

    public function activityPlans(): HasMany
    {
        return $this->hasMany(ActivityPlan::class);
    }
}
