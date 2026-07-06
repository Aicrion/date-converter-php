<?php

declare(strict_types=1);

namespace Aicrion\DateConverter;

use Aicrion\DateConverter\Calendars\CalendarRegistry;
use Aicrion\DateConverter\Enum\CalendarType;
use Aicrion\DateConverter\Exceptions\InvalidDateException;
use Aicrion\DateConverter\Exceptions\UnsupportedConversionException;
use Aicrion\DateConverter\Formatter\DateFormatter;
use Aicrion\DateConverter\Formatter\DateParser;
use Aicrion\DateConverter\ValueObjects\DateValue;
use DateTimeImmutable;
use Throwable;

/**
 * High-level facade for converting dates between Gregorian, Jalali and
 * Hijri calendars.
 *
 * Every conversion pivots internally through the Julian Day Number (JDN),
 * making the library O(1) per conversion (a handful of arithmetic
 * operations, zero loops beyond small bounded iterations in the Jalali
 * break-point scan) and trivially extensible to further calendars.
 *
 * Usage:
 *   $converter = new DateConverter();
 *   $result = $converter->convert('1404-04-15', CalendarType::Jalali, CalendarType::Gregorian);
 *   echo $result->format('Y/m/d');
 */
final class DateConverter
{
    private readonly CalendarRegistry $registry;
    private readonly DateParser $parser;

    public function __construct()
    {
        $this->registry = new CalendarRegistry();
        $this->parser = new DateParser();
    }

    /**
     * Converts a date from one calendar to another.
     *
     * @param string|array<int|string, int>|DateValue $input Source date as string, array, or DateValue.
     * @param CalendarType $from Calendar the input is expressed in.
     * @param CalendarType $to Target calendar to convert into.
     * @param string $inputPattern Pattern used to parse string input (ignored for array/DateValue input).
     *
     * @throws InvalidDateException if the source date is not a valid date in its calendar.
     * @throws UnsupportedConversionException if the JDN pivot falls outside the target calendar's algorithmic bounds.
     */
    public function convert(
        string|array|DateValue $input,
        CalendarType $from,
        CalendarType $to,
        string $inputPattern = 'Y-m-d',
    ): ConversionResult {
        $source = $input instanceof DateValue
            ? $input
            : $this->parser->parse($input, $from, $inputPattern);

        $sourceCalendar = $this->registry->resolve($from);

        if (! $sourceCalendar->isValidDate($source->year, $source->month, $source->day)) {
            throw new InvalidDateException($from, $source->year, $source->month, $source->day, 'day/month out of range or non-existent leap day');
        }

        $jdn = $sourceCalendar->toJdn($source->year, $source->month, $source->day);

        $targetCalendar = $this->registry->resolve($to);

        try {
            [$ty, $tm, $td] = $targetCalendar->fromJdn($jdn);
        } catch (Throwable $e) {
            throw new UnsupportedConversionException($from, $to, $e->getMessage());
        }

        if ($ty < $to->minYear() || $ty > $to->maxYear()) {
            throw new UnsupportedConversionException(
                $from,
                $to,
                "resulting year {$ty} is outside supported bounds for " . $to->label()
            );
        }

        $target = new DateValue($ty, $tm, $td, $to);

        return new ConversionResult($source, $target, new DateFormatter($targetCalendar));
    }

    /**
     * Checks whether a date is valid within its own calendar, without converting.
     *
     * @param string|array<int|string, int>|DateValue $input
     */
    public function isValid(string|array|DateValue $input, CalendarType $calendar, string $inputPattern = 'Y-m-d'): bool
    {
        try {
            $date = $input instanceof DateValue ? $input : $this->parser->parse($input, $calendar, $inputPattern);

            return $this->registry->resolve($calendar)->isValidDate($date->year, $date->month, $date->day);
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Checks whether converting a date from one calendar to another is possible
     * (i.e. both the source date is valid and the resulting JDN maps to a
     * representable date within the target calendar's supported bounds),
     * without throwing.
     *
     * @param string|array<int|string, int>|DateValue $input
     */
    public function canConvert(
        string|array|DateValue $input,
        CalendarType $from,
        CalendarType $to,
        string $inputPattern = 'Y-m-d',
    ): bool {
        try {
            $this->convert($input, $from, $to, $inputPattern);

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Returns today's date expressed in the given calendar.
     */
    public function today(CalendarType $calendar): ConversionResult
    {
        $now = new DateTimeImmutable('now');
        $gregorian = new DateValue((int) $now->format('Y'), (int) $now->format('n'), (int) $now->format('j'), CalendarType::Gregorian);

        return $this->convert($gregorian, CalendarType::Gregorian, $calendar);
    }

    /**
     * Direct access to the underlying calendar implementation, for advanced use
     * cases (leap year checks, days-in-month, month names, etc.) without
     * performing a full conversion.
     */
    public function calendar(CalendarType $type): \Aicrion\DateConverter\Contracts\CalendarInterface
    {
        return $this->registry->resolve($type);
    }
}
