<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\ValueObjects;

use Aicrion\DateConverter\Enum\CalendarType;

/**
 * Immutable representation of a calendar date (year, month, day)
 * tied to a specific calendar system. Internally every conversion
 * pivots through the Julian Day Number (JDN), so this value object
 * intentionally carries no conversion logic of its own.
 */
final readonly class DateValue
{
    public function __construct(
        public int $year,
        public int $month,
        public int $day,
        public CalendarType $calendar,
    ) {
    }

    /**
     * Returns a new instance with the day/month/year zero-padded array shape.
     *
     * @return array{year:int, month:int, day:int, calendar:string}
     */
    public function toArray(): array
    {
        return [
            'year' => $this->year,
            'month' => $this->month,
            'day' => $this->day,
            'calendar' => $this->calendar->value,
        ];
    }

    public function withCalendar(CalendarType $calendar): self
    {
        return new self($this->year, $this->month, $this->day, $calendar);
    }

    public function equals(DateValue $other): bool
    {
        return $this->year === $other->year
            && $this->month === $other->month
            && $this->day === $other->day
            && $this->calendar === $other->calendar;
    }

    public function __toString(): string
    {
        return sprintf('%04d-%02d-%02d', $this->year, $this->month, $this->day);
    }
}
