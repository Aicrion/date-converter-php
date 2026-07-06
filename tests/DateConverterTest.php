<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\Tests;

use Aicrion\DateConverter\DateConverter;
use Aicrion\DateConverter\Enum\CalendarType;
use Aicrion\DateConverter\Exceptions\InvalidDateException;
use Aicrion\DateConverter\ValueObjects\DateValue;
use PHPUnit\Framework\TestCase;

final class DateConverterTest extends TestCase
{
    private DateConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new DateConverter();
    }

    public function testJalaliToGregorianStringInput(): void
    {
        $result = $this->converter->convert('1404-04-15', CalendarType::Jalali, CalendarType::Gregorian);

        $this->assertSame('2025-07-06', $result->format('Y-m-d'));
    }

    public function testGregorianToJalaliArrayInput(): void
    {
        $result = $this->converter->convert(['year' => 2025, 'month' => 7, 'day' => 6], CalendarType::Gregorian, CalendarType::Jalali);

        $this->assertSame(1404, $result->year());
        $this->assertSame(4, $result->month());
        $this->assertSame(15, $result->day());
    }

    public function testGregorianToHijri(): void
    {
        $result = $this->converter->convert('2025-06-27', CalendarType::Gregorian, CalendarType::Hijri, 'Y-m-d');

        $this->assertSame(1447, $result->year());
        $this->assertSame(1, $result->month());
        $this->assertSame(1, $result->day());
    }

    public function testHijriToJalali(): void
    {
        $result = $this->converter->convert(['1447', '1', '1'], CalendarType::Hijri, CalendarType::Jalali);

        $this->assertInstanceOf(\Aicrion\DateConverter\ConversionResult::class, $result);
    }

    public function testDateValueInputIsAccepted(): void
    {
        $source = new DateValue(1404, 4, 15, CalendarType::Jalali);
        $result = $this->converter->convert($source, CalendarType::Jalali, CalendarType::Gregorian);

        $this->assertSame('2025-07-06', $result->format());
    }

    public function testCustomInputPatternWithSlashes(): void
    {
        $result = $this->converter->convert('1404/04/15', CalendarType::Jalali, CalendarType::Gregorian, 'Y/m/d');

        $this->assertSame('2025-07-06', $result->format('Y-m-d'));
    }

    public function testInvalidLeapDayThrowsException(): void
    {
        $this->expectException(InvalidDateException::class);

        $this->converter->convert('1404-12-30', CalendarType::Jalali, CalendarType::Gregorian);
    }

    public function testInvalidMonthThrowsException(): void
    {
        $this->expectException(InvalidDateException::class);

        $this->converter->convert('2025-13-01', CalendarType::Gregorian, CalendarType::Jalali);
    }

    public function testIsValidReturnsFalseForBadDate(): void
    {
        $this->assertFalse($this->converter->isValid('2023-02-30', CalendarType::Gregorian));
    }

    public function testIsValidReturnsTrueForGoodDate(): void
    {
        $this->assertTrue($this->converter->isValid('1404-04-15', CalendarType::Jalali));
    }

    public function testCanConvertReturnsFalseInsteadOfThrowing(): void
    {
        $this->assertFalse($this->converter->canConvert('1404-12-30', CalendarType::Jalali, CalendarType::Gregorian));
    }

    public function testCanConvertReturnsTrueForValidConversion(): void
    {
        $this->assertTrue($this->converter->canConvert('1404-04-15', CalendarType::Jalali, CalendarType::Gregorian));
    }

    public function testFormattedOutputWithMonthNames(): void
    {
        $result = $this->converter->convert('2025-07-06', CalendarType::Gregorian, CalendarType::Jalali);

        $this->assertSame('تیر', $result->toArray()['month_name']);
    }

    public function testArrayOutputShape(): void
    {
        $result = $this->converter->convert('1404-04-15', CalendarType::Jalali, CalendarType::Gregorian);
        $array = $result->toArray();

        $this->assertArrayHasKey('year', $array);
        $this->assertArrayHasKey('month', $array);
        $this->assertArrayHasKey('day', $array);
        $this->assertArrayHasKey('formatted', $array);
        $this->assertArrayHasKey('month_name', $array);
        $this->assertArrayHasKey('week_day', $array);
        $this->assertArrayHasKey('is_leap_year', $array);
    }

    public function testToDateTimeReturnsCorrectInstant(): void
    {
        $result = $this->converter->convert('1404-04-15', CalendarType::Jalali, CalendarType::Gregorian);
        $dt = $result->toDateTime();

        $this->assertSame('2025-07-06', $dt->format('Y-m-d'));
    }

    public function testTodayInJalaliReturnsResult(): void
    {
        $result = $this->converter->today(CalendarType::Jalali);

        $this->assertInstanceOf(\Aicrion\DateConverter\ConversionResult::class, $result);
    }

    public function testSameCalendarConversionIsIdentity(): void
    {
        $result = $this->converter->convert('2025-07-06', CalendarType::Gregorian, CalendarType::Gregorian);

        $this->assertSame('2025-07-06', $result->format());
    }
}
