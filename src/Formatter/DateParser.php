<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\Formatter;

use Aicrion\DateConverter\Enum\CalendarType;
use Aicrion\DateConverter\Exceptions\DateParseException;
use Aicrion\DateConverter\ValueObjects\DateValue;

/**
 * Parses date strings (or arrays) into DateValue instances.
 * Supports common separators (-, /, .) and explicit array input
 * of shape [year, month, day] or ['year'=>, 'month'=>, 'day'=>].
 */
final class DateParser
{
    /**
     * @param string|array<int|string, int> $input
     */
    public function parse(string|array $input, CalendarType $calendar, string $pattern = 'Y-m-d'): DateValue
    {
        if (is_array($input)) {
            return $this->parseArray($input, $calendar);
        }

        return $this->parseString($input, $calendar, $pattern);
    }

    /**
     * @param array<int|string, int> $input
     */
    private function parseArray(array $input, CalendarType $calendar): DateValue
    {
        if (isset($input['year'], $input['month'], $input['day'])) {
            return new DateValue((int) $input['year'], (int) $input['month'], (int) $input['day'], $calendar);
        }

        $values = array_values($input);

        if (count($values) < 3) {
            throw new DateParseException(json_encode($input) ?: '[]');
        }

        return new DateValue((int) $values[0], (int) $values[1], (int) $values[2], $calendar);
    }

    private function parseString(string $input, CalendarType $calendar, string $pattern): DateValue
    {
        $normalized = str_replace(['/', '.'], '-', trim($input));
        $normalizedPattern = str_replace(['/', '.'], '-', $pattern);

        $patternParts = preg_split('/-/', $normalizedPattern) ?: [];
        $valueParts = preg_split('/-/', $normalized) ?: [];

        if (count($patternParts) !== 3 || count($valueParts) !== 3) {
            throw new DateParseException($input, $pattern);
        }

        $map = [];
        foreach ($patternParts as $index => $token) {
            $key = match (true) {
                str_contains($token, 'Y') || str_contains($token, 'y') => 'year',
                str_contains($token, 'm') || str_contains($token, 'n') => 'month',
                str_contains($token, 'd') || str_contains($token, 'j') => 'day',
                default => throw new DateParseException($input, $pattern),
            };

            if (! ctype_digit($valueParts[$index])) {
                throw new DateParseException($input, $pattern);
            }

            $map[$key] = (int) $valueParts[$index];
        }

        if (! isset($map['year'], $map['month'], $map['day'])) {
            throw new DateParseException($input, $pattern);
        }

        return new DateValue($map['year'], $map['month'], $map['day'], $calendar);
    }
}
