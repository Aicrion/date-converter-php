---
layout: default
title: Getting Started
nav_order: 2
---

# Getting Started

## Requirements

- PHP 8.2 or higher
- Composer

## Installation

```bash
composer require aicrion/date-converter
```

## Basic usage

```php
use Aicrion\DateConverter\DateConverter;
use Aicrion\DateConverter\Enum\CalendarType;

$converter = new DateConverter();

$result = $converter->convert(
    input: '1404-04-15',
    from: CalendarType::Jalali,
    to: CalendarType::Gregorian
);

echo $result->year();   // 2025
echo $result->month();  // 7
echo $result->day();    // 6
echo $result->format(); // 2025-07-06
```

## Input formats

The `convert()` method accepts three input shapes:

### String

```php
$converter->convert('1404-04-15', CalendarType::Jalali, CalendarType::Gregorian);
$converter->convert('1404/04/15', CalendarType::Jalali, CalendarType::Gregorian, 'Y/m/d');
```

The fourth argument is the parsing pattern for the **source** string. Supported tokens: `Y`/`y` (year), `m`/`n` (month), `d`/`j` (day), separated by `-`, `/`, or `.`.

### Array

```php
$converter->convert([1404, 4, 15], CalendarType::Jalali, CalendarType::Gregorian);
$converter->convert(['year' => 1404, 'month' => 4, 'day' => 15], CalendarType::Jalali, CalendarType::Gregorian);
```

### DateValue object

```php
use Aicrion\DateConverter\ValueObjects\DateValue;

$source = new DateValue(1404, 4, 15, CalendarType::Jalali);
$converter->convert($source, CalendarType::Jalali, CalendarType::Gregorian);
```

## Output formats

### Formatted string

```php
$result->format('Y-m-d');   // 2025-07-06
$result->format('Y/m/d');   // 2025/07/06
$result->format('j F Y');   // 6 July 2025
$result->format('l, j F Y'); // Sunday, 6 July 2025
```

### Structured array

```php
$result->toArray('Y/m/d');
/*
[
    'year' => 2025,
    'month' => 7,
    'day' => 6,
    'calendar' => 'gregorian',
    'formatted' => '2025/07/06',
    'month_name' => 'July',
    'week_day' => 'Sunday',
    'is_leap_year' => false,
]
*/
```

### Native DateTimeImmutable

```php
$dt = $result->toDateTime(); // DateTimeImmutable instance (Gregorian-equivalent instant)
```

Next: [Format Tokens](format-tokens.html) →
