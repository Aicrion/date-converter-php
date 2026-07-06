<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\Tests;

use Aicrion\DateConverter\Calendars\GregorianCalendar;
use PHPUnit\Framework\TestCase;

final class GregorianCalendarTest extends TestCase
{
    private GregorianCalendar $calendar;

    protected function setUp(): void
    {
        $this->calendar = new GregorianCalendar();
    }

    public function testJdnRoundTrip(): void
    {
        $jdn = $this->calendar->toJdn(2026, 7, 6);
        [$y, $m, $d] = $this->calendar->fromJdn($jdn);

        $this->assertSame([2026, 7, 6], [$y, $m, $d]);
    }

    /**
     * @dataProvider leapYearProvider
     */
    public function testIsLeapYear(int $year, bool $expected): void
    {
        $this->assertSame($expected, $this->calendar->isLeapYear($year));
    }

    public static function leapYearProvider(): array
    {
        return [
            [2024, true],
            [2023, false],
            [2000, true],
            [1900, false],
            [2400, true],
        ];
    }

    public function testDaysInFebruaryLeapYear(): void
    {
        $this->assertSame(29, $this->calendar->daysInMonth(2024, 2));
        $this->assertSame(28, $this->calendar->daysInMonth(2023, 2));
    }

    public function testInvalidDateFebruary30th(): void
    {
        $this->assertFalse($this->calendar->isValidDate(2023, 2, 30));
    }

    public function testInvalidMonthZero(): void
    {
        $this->assertFalse($this->calendar->isValidDate(2023, 0, 1));
    }

    public function testValidDate(): void
    {
        $this->assertTrue($this->calendar->isValidDate(2026, 7, 6));
    }
}
