---
layout: default
title: Format Tokens
nav_order: 3
---

# Format Tokens

The `format()` and `toArray()` methods accept a pattern string using PHP `date()`-style tokens.

| Token | Meaning | Example |
|---|---|---|
| `Y` | 4-digit year, zero-padded | 1404 |
| `y` | 2-digit year | 04 |
| `m` | 2-digit month, zero-padded | 04 |
| `n` | Month without leading zero | 4 |
| `d` | 2-digit day, zero-padded | 15 |
| `j` | Day without leading zero | 15 |
| `F` | Full month name (calendar-specific) | تیر / July / محرم |
| `M` | Short month name (first 3 chars) | تیر / Jul |
| `l` | Full weekday name (calendar-specific) | یکشنبه / Sunday |
| `D` | Short weekday name (first 3 chars) | یکش / Sun |
| `N` | ISO weekday number (1 = Monday ... 7 = Sunday) | 1 |
| `w` | Weekday number (0 = Sunday ... 6 = Saturday) | 0 |

## Examples

```php
$result->format('Y-m-d');    // 1404-04-15
$result->format('Y/m/d');    // 1404/04/15
$result->format('j F Y');    // 15 تیر 1404
$result->format('l، j F Y'); // یکشنبه، 15 تیر 1404
$result->format('Y-n-j');    // 1404-4-15
```

## Month and weekday names per calendar

Each calendar implementation defines its own localized month and weekday names:

- **Gregorian** — English (January, February, ... / Sunday, Monday, ...)
- **Jalali** — Persian (فروردین, اردیبهشت, ... / یکشنبه, دوشنبه, ...)
- **Hijri** — Arabic (محرم, صفر, ... / الأحد, الإثنين, ...)

Next: [Error Handling](error-handling.html) →
