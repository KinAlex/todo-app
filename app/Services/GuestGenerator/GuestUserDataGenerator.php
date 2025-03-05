<?php

declare(strict_types=1);

namespace App\Services\GuestGenerator;

use Illuminate\Support\Str;

class GuestUserDataGenerator
{
    private const string GUEST_NAME = 'guest';

    public function get(): UserGuestDTO
    {
        $pass = Str::password();

        return new UserGuestDTO(
            self::GUEST_NAME,
            Str::random().'@gmail.com',
            $pass,
            $pass,
        );
    }
}
