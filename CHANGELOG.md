# Changelog

All notable changes to `aicrion/date-converter` are documented here.
This project follows [Semantic Versioning](https://semver.org/).

## [1.0.0] - 2026-07-06

### Added
- Initial release of the library.
- `Gregorian`, `Jalali` (Persian/Shamsi), and `Hijri` (Islamic/Qamari) calendar support.
- Julian Day Number (JDN) pivot architecture for O(1) conversions between any two supported calendars.
- Birashk/Borkowski algorithm for accurate Jalali leap-year handling across a wide historical range.
- Tabular (civil) algorithm for Hijri conversions.
- `DateConverter` facade with `convert()`, `isValid()`, `canConvert()`, `today()`, and `calendar()`.
- Flexible input support: strings (with custom patterns), arrays (indexed or associative), and `DateValue` objects.
- Flexible output support: formatted strings, structured arrays, and native `DateTimeImmutable`.
- Full PHPUnit test suite covering round-trip conversions, leap years, and invalid date detection.
