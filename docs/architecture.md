---
layout: default
title: Architecture
nav_order: 5
---

# Architecture

## The JDN pivot

Every calendar in this library implements `CalendarInterface`, which requires exactly two conversion methods:

```php
public function toJdn(int $year, int $month, int $day): int;
public function fromJdn(int $jdn): array; // [year, month, day]
```

The **Julian Day Number** (a continuous day count with no calendar-specific concept of months or leap years) is the single pivot every conversion passes through:

```
Source calendar (Y, M, D) --toJdn()--> JDN --fromJdn()--> Target calendar (Y, M, D)
```

This means converting from Jalali to Hijri never touches Jalali-specific or Hijri-specific logic directly — it goes Jalali → JDN → Hijri. Adding a fourth calendar (say, Hebrew) only requires writing one new class; no existing class changes.

## Components

| Component | Responsibility |
|---|---|
| `CalendarInterface` | Contract every calendar implementation must satisfy |
| `GregorianCalendar`, `JalaliCalendar`, `HijriCalendar` | Pure algorithmic conversion to/from JDN, validation, leap years, month/weekday names |
| `CalendarRegistry` | Memoizes calendar instances so `DateConverter` never re-instantiates them on every call |
| `DateValue` | Immutable (`readonly`) representation of a year/month/day tied to a calendar |
| `DateParser` | Turns strings or arrays into `DateValue` instances |
| `DateFormatter` | Turns `DateValue` instances into formatted strings, arrays, or `DateTimeImmutable` |
| `DateConverter` | Facade orchestrating parse → validate → toJdn → fromJdn → format |
| `ConversionResult` | Convenience wrapper around the converted `DateValue`, exposing `format()`, `toArray()`, `toDateTime()` |

## Why JDN and not native PHP DateTime?

PHP's native `DateTime`/`DateTimeImmutable` classes have no concept of the Jalali or Hijri calendar — they are Gregorian-only. Using JDN as an calendar-agnostic integer pivot lets every calendar be treated symmetrically, and keeps every conversion to a small, constant number of arithmetic operations (no loops beyond the bounded 20-element break-point scan in the Jalali algorithm).

## Algorithms used

- **Gregorian ↔ JDN** — the standard Fliegel & Van Flandern proleptic Gregorian algorithm.
- **Jalali ↔ JDN** — the Birashk/Borkowski break-point algorithm (same algorithmic basis as `jalaali-js`/`jalaali-php`), correctly handling the irregular 33-year leap cycle across a wide historical range.
- **Hijri ↔ JDN** — the tabular (civil) Islamic calendar algorithm, the standard approximation used across software systems absent real moon-sighting data.

## Extending with a new calendar

1. Create a class implementing `CalendarInterface` in `src/Calendars/`.
2. Add a new case to `CalendarType` enum with its `label()`, `minYear()`, `maxYear()`.
3. Register it in `CalendarRegistry::resolve()`'s `match` expression.

No other class needs to change — `DateConverter`, `DateFormatter`, and `DateParser` all operate purely against `CalendarInterface`.
