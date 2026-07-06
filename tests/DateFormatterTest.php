<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\Tests;

use Aicrion\DateConverter\Calendars\JalaliCalendar;
use Aicrion\DateConverter\Enum\CalendarType;
use Aicrion\DateConverter\Formatter\DateFormatter;
use Aicrion\DateConverter\ValueObjects\DateValue;
use PHPUnit\Framework\TestCase;

final class DateFormatterTest extends TestCase
{
    private DateFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new DateFormatter(new JalaliCalendar());
    }

    public function testDefaultFormat(): void
    {
        $date = new DateValue(1404, 4, 15, CalendarType::Jalali);

        $this->assertSame('1404-04-15', $this->formatter->format($date));
    }

    public function testSlashFormat(): void
    {
        $date = new DateValue(1404, 4, 15, CalendarType::Jalali);

        $this->assertSame('1404/04/15', $this->formatter->format($date, 'Y/m/d'));
    }

    public function testMonthNameFormat(): void
    {
        $date = new DateValue(1404, 4, 15, CalendarType::Jalali);

        $this->assertSame('15 تیر 1404', $this->formatter->format($date, 'j F Y'));
    }

    public function testShortNumericTokens(): void
    {
        $date = new DateValue(1404, 4, 5, CalendarType::Jalali);

        $this->assertSame('1404-4-5', $this->formatter->format($date, 'Y-n-j'));
    }

    public function testToArrayContainsFormattedKey(): void
    {
        $date = new DateValue(1404, 4, 15, CalendarType::Jalali);
        $array = $this->formatter->toArray($date, 'Y/m/d');

        $this->assertSame('1404/04/15', $array['formatted']);
    }
}
