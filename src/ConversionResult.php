<?php

declare(strict_types=1);

namespace Aicrion\DateConverter;

use Aicrion\DateConverter\Formatter\DateFormatter;
use Aicrion\DateConverter\ValueObjects\DateValue;
use DateTimeImmutable;

/**
 * Represents the outcome of a calendar conversion, exposing both the
 * original source date and the converted target date, plus convenient
 * formatting helpers scoped to the target calendar.
 */
final readonly class ConversionResult
{
    public function __construct(
        public DateValue $source,
        public DateValue $target,
        private DateFormatter $formatter,
    ) {
    }

    /**
     * Formats the target date using PHP date()-style tokens (Y, m, d, n, j, F, M, l, D, N, w).
     */
    public function format(string $pattern = 'Y-m-d'): string
    {
        return $this->formatter->format($this->target, $pattern);
    }

    /**
     * @return array{year:int, month:int, day:int, calendar:string, formatted:string, month_name:string, week_day:string, is_leap_year:bool}
     */
    public function toArray(string $pattern = 'Y-m-d'): array
    {
        return $this->formatter->toArray($this->target, $pattern);
    }

    /**
     * Converts the target date into its equivalent Gregorian DateTimeImmutable instant.
     */
    public function toDateTime(): DateTimeImmutable
    {
        return $this->formatter->toDateTime($this->target);
    }

    public function year(): int
    {
        return $this->target->year;
    }

    public function month(): int
    {
        return $this->target->month;
    }

    public function day(): int
    {
        return $this->target->day;
    }

    public function isLeapYear(): bool
    {
        return $this->formatter->toArray($this->target)['is_leap_year'];
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
