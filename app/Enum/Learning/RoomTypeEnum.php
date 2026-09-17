<?php

namespace App\Enum\Learning;

enum RoomTypeEnum: string
{
    case ONLINE = 'online';
    case OFFLINE = 'offline';

    public function label(): string
    {
        return match ($this) {
            self::ONLINE => 'Daring/Online(zoom)',
            self::OFFLINE => 'Tatap Muka(Offline)',
        };
    }
}
