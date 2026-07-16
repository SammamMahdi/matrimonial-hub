<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Server-side validation. The original app relied on HTML `required` and
 * `<option>` lists alone, so a hand-crafted POST could write any value into
 * an ENUM column. Every rule here runs on the server, where it counts.
 */
final class Validator
{
    private array $errors = [];

    public function __construct(private array $data)
    {
    }

    public static function make(array $data): self
    {
        return new self($data);
    }

    public function required(string $field, string $label): self
    {
        $value = $this->data[$field] ?? '';

        if (!is_string($value) || trim($value) === '') {
            $this->addError($field, "{$label} is required.");
        }

        return $this;
    }

    public function email(string $field, string $label = 'Email'): self
    {
        $value = $this->data[$field] ?? '';

        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "{$label} must be a valid email address.");
        }

        return $this;
    }

    public function minLength(string $field, int $min, string $label): self
    {
        $value = (string) ($this->data[$field] ?? '');

        if ($value !== '' && mb_strlen($value) < $min) {
            $this->addError($field, "{$label} must be at least {$min} characters.");
        }

        return $this;
    }

    public function maxLength(string $field, int $max, string $label): self
    {
        $value = (string) ($this->data[$field] ?? '');

        if (mb_strlen($value) > $max) {
            $this->addError($field, "{$label} must be {$max} characters or fewer.");
        }

        return $this;
    }

    public function matches(string $field, string $other, string $label): self
    {
        if (($this->data[$field] ?? null) !== ($this->data[$other] ?? null)) {
            $this->addError($field, "{$label} does not match.");
        }

        return $this;
    }

    /** @param array<int,string|int> $allowed */
    public function in(string $field, array $allowed, string $label): self
    {
        $value = $this->data[$field] ?? '';

        if ($value !== '' && !in_array($value, $allowed, true)) {
            $this->addError($field, "{$label} is not a valid choice.");
        }

        return $this;
    }

    public function numericBetween(string $field, float $min, float $max, string $label): self
    {
        $value = $this->data[$field] ?? '';

        if ($value === '' || $value === null) {
            return $this;
        }

        if (!is_numeric($value)) {
            $this->addError($field, "{$label} must be a number.");

            return $this;
        }

        $number = (float) $value;

        if ($number < $min || $number > $max) {
            $this->addError($field, "{$label} must be between {$min} and {$max}.");
        }

        return $this;
    }

    /** Validates a date and enforces a minimum age in years. */
    public function dateOfBirth(string $field, int $minAge, string $label = 'Date of birth'): self
    {
        $value = (string) ($this->data[$field] ?? '');

        if ($value === '') {
            return $this;
        }

        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $value);

        if ($date === false || $date->format('Y-m-d') !== $value) {
            $this->addError($field, "{$label} must be a valid date.");

            return $this;
        }

        $now = new \DateTimeImmutable('today');

        if ($date > $now) {
            $this->addError($field, "{$label} cannot be in the future.");

            return $this;
        }

        if ($date->diff($now)->y < $minAge) {
            $this->addError($field, "You must be at least {$minAge} years old to register.");
        }

        return $this;
    }

    public function addError(string $field, string $message): self
    {
        $this->errors[$field][] = $message;

        return $this;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function passes(): bool
    {
        return $this->errors === [];
    }

    /** @return array<string,list<string>> */
    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        foreach ($this->errors as $messages) {
            return $messages[0];
        }

        return null;
    }
}
