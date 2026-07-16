<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Compatibility scoring.
 *
 * The original site displayed "Match Strength: <?= rand(70, 100) ?>%" — a
 * number re-rolled on every page load with no relationship to the two people
 * involved. This computes a real score from the viewer's saved preferences and
 * returns the reasons behind it, so the UI can explain itself.
 *
 * Each criterion contributes its weight only when it applies (i.e. the viewer
 * expressed that preference and the candidate published that field). The score
 * is the achieved weight over the applicable weight, so someone who filled in
 * two preferences is not punished against someone who filled in eight.
 */
final class Matcher
{
    private const WEIGHTS = [
        'age'            => 22,
        'religion'       => 20,
        'interests'      => 16,
        'marital_status' => 12,
        'ethnicity'      => 10,
        'height'         => 8,
        'profession'     => 7,
        'education'      => 5,
    ];

    /**
     * @param  array<string,mixed> $preferences viewer's saved preferences row
     * @param  array<string,mixed> $candidate   joined user + profile row
     * @return array{score:int, reasons:list<string>}
     */
    public static function score(array $preferences, array $candidate): array
    {
        $applicable = 0.0;
        $achieved   = 0.0;
        $reasons    = [];

        // --- Age ------------------------------------------------------------
        $minAge = self::intOrNull($preferences['min_age'] ?? null);
        $maxAge = self::intOrNull($preferences['max_age'] ?? null);
        $age    = age_from($candidate['dob'] ?? null);

        if ($age !== null && ($minAge !== null || $maxAge !== null)) {
            $applicable += self::WEIGHTS['age'];
            $low        = $minAge ?? 18;
            $high       = $maxAge ?? 120;

            if ($age >= $low && $age <= $high) {
                $achieved += self::WEIGHTS['age'];
                $reasons[] = "Age {$age} is inside your {$low}–{$high} range";
            } else {
                // Near misses still earn partial credit, tapering over 6 years.
                $distance = $age < $low ? $low - $age : $age - $high;
                $partial  = max(0.0, 1 - ($distance / 6));
                $achieved += self::WEIGHTS['age'] * $partial;
            }
        }

        // --- Religion -------------------------------------------------------
        $achieved += self::exact(
            $preferences['preferred_religion'] ?? null,
            $candidate['religion'] ?? null,
            self::WEIGHTS['religion'],
            $applicable,
            $reasons,
            fn ($v) => "Both {$v}"
        );

        // --- Shared interests and hobbies -----------------------------------
        $viewerTags    = self::tags(($preferences['interests'] ?? '') . ',' . ($preferences['hobbies'] ?? ''));
        $candidateTags = self::tags(($candidate['interests'] ?? '') . ',' . ($candidate['hobbies'] ?? ''));

        if ($viewerTags !== [] && $candidateTags !== []) {
            $applicable += self::WEIGHTS['interests'];
            $shared      = array_values(array_intersect($viewerTags, $candidateTags));

            if ($shared !== []) {
                // Full credit at three or more shared tags.
                $ratio     = min(1.0, count($shared) / 3);
                $achieved += self::WEIGHTS['interests'] * $ratio;
                $reasons[] = 'Shared interest in ' . implode(', ', array_slice($shared, 0, 3));
            }
        }

        // --- Marital status -------------------------------------------------
        $achieved += self::exact(
            $preferences['preferred_marital_status'] ?? null,
            $candidate['marital_status'] ?? null,
            self::WEIGHTS['marital_status'],
            $applicable,
            $reasons,
            fn ($v) => "Marital status: {$v}"
        );

        // --- Ethnicity ------------------------------------------------------
        $achieved += self::exact(
            $preferences['preferred_ethnicity'] ?? null,
            $candidate['ethnicity'] ?? null,
            self::WEIGHTS['ethnicity'],
            $applicable,
            $reasons,
            fn ($v) => "Ethnicity: {$v}"
        );

        // --- Height ---------------------------------------------------------
        $minHeight = self::floatOrNull($preferences['min_height_cm'] ?? null);
        $maxHeight = self::floatOrNull($preferences['max_height_cm'] ?? null);
        $height    = self::floatOrNull($candidate['height_cm'] ?? null);

        if ($height !== null && $height > 0 && ($minHeight !== null || $maxHeight !== null)) {
            $applicable += self::WEIGHTS['height'];
            $low         = $minHeight ?? 0;
            $high        = $maxHeight ?? 300;

            if ($height >= $low && $height <= $high) {
                $achieved += self::WEIGHTS['height'];
                $reasons[] = 'Height is inside your preferred range';
            } else {
                $distance = $height < $low ? $low - $height : $height - $high;
                $achieved += self::WEIGHTS['height'] * max(0.0, 1 - ($distance / 15));
            }
        }

        // --- Profession field -----------------------------------------------
        $preferredProfession = self::stringOrNull($preferences['preferred_profession'] ?? null);
        $candidateProfession = self::stringOrNull($candidate['profession'] ?? null);

        if ($preferredProfession !== null && $candidateProfession !== null) {
            $applicable += self::WEIGHTS['profession'];

            if ($preferredProfession === $candidateProfession) {
                $achieved += self::WEIGHTS['profession'];
                $reasons[] = 'Works as a ' . humanise($candidateProfession);
            } else {
                $wantedField = Vocabulary::professionField($preferredProfession);
                $theirField  = Vocabulary::professionField($candidateProfession);

                // Same field is a decent proxy for shared working life.
                if ($wantedField !== null && $wantedField === $theirField) {
                    $achieved += self::WEIGHTS['profession'] * 0.6;
                    $reasons[] = "Also works in {$theirField}";
                }
            }
        }

        // --- Education ------------------------------------------------------
        $wantedEducation = self::stringOrNull($preferences['preferred_education'] ?? null);

        if ($wantedEducation !== null) {
            $applicable += self::WEIGHTS['education'];
            $theirs = array_filter([
                self::stringOrNull($candidate['undergraduate'] ?? null),
                self::stringOrNull($candidate['postgraduate'] ?? null),
            ]);

            if (in_array($wantedEducation, $theirs, true)) {
                $achieved += self::WEIGHTS['education'];
                $reasons[] = "Holds a {$wantedEducation}";
            }
        }

        if ($applicable <= 0.0) {
            // No preferences saved yet — say so rather than inventing a number.
            return ['score' => 0, 'reasons' => []];
        }

        return [
            'score'   => (int) round(($achieved / $applicable) * 100),
            'reasons' => $reasons,
        ];
    }

    /**
     * Applies the score to every candidate and sorts by it, descending.
     *
     * @param  array<string,mixed>       $preferences
     * @param  list<array<string,mixed>> $candidates
     * @return list<array<string,mixed>>
     */
    public static function rank(array $preferences, array $candidates): array
    {
        foreach ($candidates as $i => $candidate) {
            $result                       = self::score($preferences, $candidate);
            $candidates[$i]['match_score']   = $result['score'];
            $candidates[$i]['match_reasons'] = $result['reasons'];
        }

        usort($candidates, static fn (array $a, array $b) => $b['match_score'] <=> $a['match_score']);

        return $candidates;
    }

    /** Scores an exact-equality criterion, accumulating weight by reference. */
    private static function exact(
        mixed $wanted,
        mixed $actual,
        float $weight,
        float &$applicable,
        array &$reasons,
        callable $reason
    ): float {
        $wanted = self::stringOrNull($wanted);
        $actual = self::stringOrNull($actual);

        if ($wanted === null || $actual === null) {
            return 0.0;
        }

        $applicable += $weight;

        if ($wanted !== $actual) {
            return 0.0;
        }

        $reasons[] = $reason($actual);

        return $weight;
    }

    /**
     * Splits free text into comparable tags. Lower-cased and de-duplicated so
     * "Reading, cooking" and "cooking; reading" compare equal.
     *
     * @return list<string>
     */
    private static function tags(string $text): array
    {
        $parts = preg_split('/[,;\/\n]+/', mb_strtolower($text)) ?: [];
        $tags  = [];

        foreach ($parts as $part) {
            $part = trim($part);

            if ($part !== '' && mb_strlen($part) > 2) {
                $tags[] = $part;
            }
        }

        return array_values(array_unique($tags));
    }

    private static function stringOrNull(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private static function intOrNull(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private static function floatOrNull(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }
}
