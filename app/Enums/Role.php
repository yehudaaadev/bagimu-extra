<?php

namespace App\Enums;

enum Role: string
{
    case STUDENT = 'STUDENT';
    case ADMIN = 'ADMIN';
    case PEMBINA = 'PEMBINA';
    case OPERATOR = 'OPERATOR';
    case WALI_KELAS = 'WALI_KELAS';

    public function label(): string
    {
        return match ($this) {
            self::STUDENT => 'Siswa',
            self::ADMIN => 'Admin',
            self::PEMBINA => 'Pembina',
            self::OPERATOR => 'Operator',
            self::WALI_KELAS => 'Wali Kelas',
        };
    }
}
