<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\Tests;

use Aicrion\DateConverter\Calendars\HijriCalendar;
use PHPUnit\Framework\TestCase;

final class HijriCalendarTest extends TestCase
{
    private HijriCalendar $calendar;

    protected function setUp(): void
    {
        $this->calendar = new HijriCalendar();
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
            [1447, 1, 1],
            [1446, 12, 29],
            [1400, 6, 15],
            [1, 1, 1],
            [1445, 9, 30],
        ];
    }

    public function testKnownGregorianEquivalent(): void
    {
        $gregorian = new \Aicrion\DateConverter\Calendars\GregorianCalendar();
        $jdn = $this->calendar->toJdn(1447, 1, 1);

        $this->assertSame([2025, 6, 27], $gregorian->fromJdn($jdn));
    }

    public function testOddMonthsHave30Days(): void
    {
        $this->assertSame(30, $this->calendar->daysInMonth(1447, 1));
        $this->assertSame(30, $this->calendar->daysInMonth(1447, 9));
    }

    public function testEvenMonthsHave29Days(): void
    {
        $this->assertSame(29, $this->calendar->daysInMonth(1447, 2));
    }

    public function testInvalidMonth13(): void
    {
        $this->assertFalse($this->calendar->isValidDate(1447, 13, 1));
    }

    public function testInvalidDayZero(): void
    {
        $this->assertFalse($this->calendar->isValidDate(1447, 1, 0));
    }
}
