<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\Tests;

use Aicrion\DateConverter\Enum\CalendarType;
use Aicrion\DateConverter\Exceptions\DateParseException;
use Aicrion\DateConverter\Formatter\DateParser;
use PHPUnit\Framework\TestCase;

final class DateParserTest extends TestCase
{
    private DateParser $parser;

    protected function setUp(): void
    {
        $this->parser = new DateParser();
    }

    public function testParsesDashSeparatedString(): void
    {
        $date = $this->parser->parse('1404-04-15', CalendarType::Jalali);

        $this->assertSame(1404, $date->year);
        $this->assertSame(4, $date->month);
        $this->assertSame(15, $date->day);
    }

    public function testParsesSlashSeparatedStringWithCustomPattern(): void
    {
        $date = $this->parser->parse('1404/04/15', CalendarType::Jalali, 'Y/m/d');

        $this->assertSame(1404, $date->year);
        $this->assertSame(4, $date->month);
        $this->assertSame(15, $date->day);
    }

    public function testParsesIndexedArray(): void
    {
        $date = $this->parser->parse([1404, 4, 15], CalendarType::Jalali);

        $this->assertSame(1404, $date->year);
    }

    public function testParsesAssociativeArray(): void
    {
        $date = $this->parser->parse(['year' => 1404, 'month' => 4, 'day' => 15], CalendarType::Jalali);

        $this->assertSame(15, $date->day);
    }

    public function testThrowsOnMalformedString(): void
    {
        $this->expectException(DateParseException::class);

        $this->parser->parse('not-a-date', CalendarType::Jalali);
    }

    public function testThrowsOnIncompleteArray(): void
    {
        $this->expectException(DateParseException::class);

        $this->parser->parse([1404, 4], CalendarType::Jalali);
    }
}
