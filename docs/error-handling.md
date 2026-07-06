---
layout: default
title: Error Handling
nav_order: 4
---

# Error Handling

The library distinguishes between two failure modes: an **invalid source date** and an **unrepresentable target date**.

## Exceptions

| Exception | Thrown when |
|---|---|
| `InvalidDateException` | The source year/month/day does not form a valid date in its own calendar (e.g. Esfand 30th in a non-leap Jalali year, February 30th, month 13). |
| `UnsupportedConversionException` | The resulting Julian Day Number does not map to a representable date within the target calendar's supported year bounds. |
| `DateParseException` | A string input cannot be parsed with the given (or inferred) pattern. |

```php
use Aicrion\DateConverter\Exceptions\InvalidDateException;
use Aicrion\DateConverter\Exceptions\UnsupportedConversionException;

try {
    $converter->convert('1404-12-30', CalendarType::Jalali, CalendarType::Gregorian);
} catch (InvalidDateException $e) {
    echo $e->getMessage();
    // Invalid Jalali (Persian/Shamsi) date: 1404-12-30 (day/month out of range or non-existent leap day)
}
```

## Checking feasibility without exceptions

Use `canConvert()` when you want a boolean answer instead of a try/catch block:

```php
if ($converter->canConvert('1404-12-30', CalendarType::Jalali, CalendarType::Gregorian)) {
    $result = $converter->convert('1404-12-30', CalendarType::Jalali, CalendarType::Gregorian);
} else {
    echo 'This date does not exist (1404 is not a leap year).';
}
```

## Validating a date without converting

`isValid()` checks a date against its own calendar's rules only (no conversion attempted):

```php
$converter->isValid('2023-02-30', CalendarType::Gregorian); // false
$converter->isValid('1403-12-30', CalendarType::Jalali);    // true (1403 is a leap year)
```

## Supported year ranges

| Calendar | Min year | Max year |
|---|---|---|
| Gregorian | 1 | 3000 |
| Jalali | -1096 | 3177 |
| Hijri | 1 | 5000 |

Converting to a target year outside these bounds raises `UnsupportedConversionException` rather than silently producing an incorrect result.

Next: [Architecture](architecture.html) →
