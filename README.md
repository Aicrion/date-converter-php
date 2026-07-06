# Aicrion Date Converter

A high-performance, zero-dependency PHP 8.2+ library for converting dates between **Gregorian**, **Jalali (Persian/Shamsi)**, and **Hijri (Islamic/Qamari)** calendars.

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.2-777bb4)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-blue)](LICENSE.md)

📖 **[Full Documentation](https://aicrion.github.io/date-converter-php/)**

## Why this library?

- **Single pivot architecture** — every calendar converts to/from a Julian Day Number (JDN), so adding a new calendar never touches existing conversion logic.
- **Astronomically accurate Jalali algorithm** — uses the Birashk/Borkowski break-point algorithm (same base as `jalaali-js`), correct across an extremely wide year range, not a naive fixed 33-year approximation.
- **Modern PHP** — readonly classes, backed enums, strict types, first-class callable syntax, PHP 8.2+ only.
- **Zero runtime dependencies.**
- **Safe by design** — every conversion validates the source date and the resulting target bounds; `canConvert()` lets you check feasibility without exceptions.

## Installation

```bash
composer require aicrion/date-converter
```

## Quick start

```php
use Aicrion\DateConverter\DateConverter;
use Aicrion\DateConverter\Enum\CalendarType;

$converter = new DateConverter();

$result = $converter->convert('1404-04-15', CalendarType::Jalali, CalendarType::Gregorian);

echo $result->format('Y-m-d');      // 2025-07-06
echo $result->format('j F Y');      // 6 July 2025
print_r($result->toArray());        // structured array output
```

## Supported input formats

```php
$converter->convert('1404-04-15', CalendarType::Jalali, CalendarType::Gregorian);
$converter->convert('1404/04/15', CalendarType::Jalali, CalendarType::Gregorian, 'Y/m/d');
$converter->convert([1404, 4, 15], CalendarType::Jalali, CalendarType::Gregorian);
$converter->convert(['year' => 1404, 'month' => 4, 'day' => 15], CalendarType::Jalali, CalendarType::Gregorian);
```

## Checking feasibility

```php
if ($converter->canConvert('1404-12-30', CalendarType::Jalali, CalendarType::Gregorian)) {
    // safe to convert
} else {
    // invalid date (e.g. Esfand 30th only exists in leap years)
}
```

## Today in any calendar

```php
$today = $converter->today(CalendarType::Jalali);
echo $today->format('Y/m/d');
```

## Documentation

Full API reference, algorithm notes, and advanced usage examples are published at:

**https://aicrion.github.io/date-converter-php/**

## Testing

```bash
composer install
composer test
```

## Benchmarks

```bash
php benchmarks/run.php
```

---

## 📜 License

Created with ❤️ by Aicrion. Licensed under the [MIT License](LICENSE.md). Free to use, modify, and distribute!
