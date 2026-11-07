<?php

namespace App\Enum\Assessment;

enum PhysicalMetricEnum: string
{
    case RUN_12_MINUTES = 'run_12_minutes';
    case PULL_UP = 'pull_up';
    case PUSH_UP = 'push_up';
    case SIT_UP = 'sit_up';
    case SHUTTLE_RUN = 'shuttle_run';

    public function label(): string
    {
        return match ($this) {
            self::RUN_12_MINUTES => 'Lari 12 Menit',
            self::PULL_UP => 'Pull-Up / Chinning',
            self::PUSH_UP => 'Push-Up (1 Menit)',
            self::SIT_UP => 'Sit-Up (1 Menit)',
            self::SHUTTLE_RUN => 'Shuttle Run 6x10m',
        };
    }

    public function unit(): string
    {
        return match ($this) {
            self::RUN_12_MINUTES => 'meter',
            self::PULL_UP, self::PUSH_UP, self::SIT_UP => 'kali',
            self::SHUTTLE_RUN => 'detik',
        };
    }

    public function calculateScore(float $rawValue): float
    {
        $score = match ($this) {
            self::RUN_12_MINUTES => ($rawValue / 3500) * 100,
            self::PULL_UP => ($rawValue / 18) * 100,
            self::PUSH_UP => ($rawValue / 45) * 100,
            self::SIT_UP => ($rawValue / 45) * 100,
            self::SHUTTLE_RUN => $rawValue <= 16.0
                ? 100.0
                : max(0.0, ((24.0 - $rawValue) / (24.0 - 16.0)) * 100),
        };

        return round(min(100.00, max(0.0, $score)), 2);
    }

    public function options(): array
    {
        return array_map(fn (self $metric) => [
            'key' => $metric->value,
            'label' => $metric->label(),
            'unit' => $metric->unit(),
        ], self::cases());
    }
}
