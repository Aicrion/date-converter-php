---
layout: default
title: Home
nav_order: 1
---

# Aicrion Date Converter

A high-performance, zero-dependency PHP 8.2+ library for converting dates between **Gregorian**, **Jalali (Persian/Shamsi)**, and **Hijri (Islamic/Qamari)** calendars.

[View on GitHub](https://github.com/aicrion/date-converter-php){: .btn }
[Get Started](getting-started.html){: .btn }

## Why this library?

- **Single JDN pivot architecture** — every calendar converts to/from a Julian Day Number, so adding a new calendar never touches existing conversion logic.
- **Astronomically accurate Jalali algorithm** — Birashk/Borkowski break-point algorithm, correct across an extremely wide year range.
- **Modern PHP 8.2+** — readonly classes, backed enums, strict types.
- **Zero runtime dependencies.**
- **Safe by design** — `canConvert()` checks feasibility without throwing exceptions; `convert()` throws descriptive exceptions when something is truly invalid.

## Supported calendars

| Calendar | Enum case | Notes |
|---|---|---|
| Gregorian | `CalendarType::Gregorian` | Standard proleptic Gregorian calendar |
| Jalali (Persian/Shamsi) | `CalendarType::Jalali` | Birashk/Borkowski algorithm, valid roughly years -1096 to 3177 |
| Hijri (Islamic/Qamari) | `CalendarType::Hijri` | Tabular (civil) algorithm |

## Quick example

```php
use Aicrion\DateConverter\DateConverter;
use Aicrion\DateConverter\Enum\CalendarType;

$converter = new DateConverter();
$result = $converter->convert('1404-04-15', CalendarType::Jalali, CalendarType::Gregorian);

echo $result->format('Y-m-d'); // 2025-07-06
```

Continue to [Getting Started](getting-started.html) →
