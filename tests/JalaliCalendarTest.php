<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\Tests;

use Aicrion\DateConverter\Calendars\JalaliCalendar;
use PHPUnit\Framework\TestCase;

final class JalaliCalendarTest extends TestCase
{
    private JalaliCalendar $calendar;

    protected function setUp(): void
    {
        $this->calendar = new JalaliCalendar();
    }

    /**
     * @dataProvider roundTripProvider
     */
    public function testJdnRoundTrip(int $y, int $m, int $d): void
    {
        $jdn = $this->calendar->toJdn($y, $m, $d);
        $this->assertSame([$y, $m, $d], $this->calendar->fromJdn($jdn));
    }

    public static function roundTripProvider(): array
    {
        return [
            [1404, 4, 15],
            [1403, 12, 29],
            [1403, 12, 30],
            [1400, 1, 1],
            [1, 1, 1],
            [1300, 6, 31],
        ];
    }

    public function testKnownGregorianEquivalent(): void
    {
        $gregorian = new \Aicrion\DateConverter\Calendars\GregorianCalendar();
        $jdn = $this->calendar->toJdn(1404, 4, 15);

        $this->assertSame([2025, 7, 6], $gregorian->fromJdn($jdn));
    }

    public function testLeapYear1403IsLeap(): void
    {
        $this->assertTrue($this->calendar->isLeapYear(1403));
    }

    public function testNonLeapYear1404(): void
    {
        $this->assertFalse($this->calendar->isLeapYear(1404));
    }

    public function testEsfand30ValidOnlyInLeapYear(): void
    {
        $this->assertTrue($this->calendar->isValidDate(1403, 12, 30));
        $this->assertFalse($this->calendar->isValidDate(1404, 12, 30));
    }

    public function testInvalidMonth13(): void
    {
        $this->assertFalse($this->calendar->isValidDate(1404, 13, 1));
    }

    public function testDaysInMonthFirstSixMonths(): void
    {
        $this->assertSame(31, $this->calendar->daysInMonth(1404, 1));
        $this->assertSame(31, $this->calendar->daysInMonth(1404, 6));
    }

    public function testDaysInMonthLastFiveMonths(): void
    {
        $this->assertSame(30, $this->calendar->daysInMonth(1404, 7));
        $this->assertSame(30, $this->calendar->daysInMonth(1404, 11));
    }
}
