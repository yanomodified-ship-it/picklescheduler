<?php

namespace App\Support;

class SkillLevels
{
    public const LEVELS = [
        'Beginner'     => ['score' => 1, 'color' => '#22C55E', 'hint' => 'Still learning the rules and basic shots'],
        'Novice'       => ['score' => 2, 'color' => '#84CC16', 'hint' => 'Knows the basics, rallies are still short'],
        'Intermediate' => ['score' => 3, 'color' => '#3B82F6', 'hint' => 'Steady rallies, understands strategy and the kitchen'],
        'Advanced'     => ['score' => 4, 'color' => '#8B5CF6', 'hint' => 'Strong, controlled play with spin and placement'],
        'Pro'          => ['score' => 5, 'color' => '#F43F5E', 'hint' => 'Tournament level, plays competitively'],
    ];

    public static function names(): array
    {
        return array_keys(self::LEVELS);
    }

    public static function color(?string $level): string
    {
        return self::LEVELS[(string) $level]['color'] ?? '#94A3B8';
    }

    /** Average level of a group of entries, counting every player. Entries without a level are ignored. */
    public static function average(iterable $entries): ?string
    {
        $sum   = 0;
        $count = 0;

        foreach ($entries as $e) {
            $score = self::LEVELS[(string) $e->skill_level]['score'] ?? null;
            if ($score === null) {
                continue;
            }
            $sum   += $score * (int) $e->players;
            $count += (int) $e->players;
        }

        if ($count === 0) {
            return null;
        }

        $avg = (int) round($sum / $count);

        foreach (self::LEVELS as $name => $meta) {
            if ($meta['score'] === $avg) {
                return $name;
            }
        }

        return null;
    }

    /** Players per level, in level order, e.g. ['Beginner' => 2, 'Intermediate' => 3]. */
    public static function counts(iterable $entries): array
    {
        $out = [];

        foreach (self::LEVELS as $name => $meta) {
            $n = 0;
            foreach ($entries as $e) {
                if ($e->skill_level === $name) {
                    $n += (int) $e->players;
                }
            }
            if ($n > 0) {
                $out[$name] = $n;
            }
        }

        return $out;
    }
}