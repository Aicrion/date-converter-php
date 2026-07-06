<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Aicrion\DateConverter\DateConverter;
use Aicrion\DateConverter\Enum\CalendarType;

$converter = new DateConverter();
$iterations = 100_000;

$scenarios = [
    'Jalali -> Gregorian' => [CalendarType::Jalali, CalendarType::Gregorian, '1404-04-15'],
    'Gregorian -> Jalali' => [CalendarType::Gregorian, CalendarType::Jalali, '2025-07-06'],
    'Gregorian -> Hijri' => [CalendarType::Gregorian, CalendarType::Hijri, '2025-06-27'],
    'Hijri -> Jalali' => [CalendarType::Hijri, CalendarType::Jalali, '1447-01-01'],
];

printf("%-24s %12s %14s\n", 'Scenario', 'Iterations', 'Conversions/sec');
echo str_repeat('-', 54) . "\n";

foreach ($scenarios as $label => [$from, $to, $input]) {
    $start = hrtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        $converter->convert($input, $from, $to);
    }

    $elapsedSeconds = (hrtime(true) - $start) / 1_000_000_000;
    $rate = (int) ($iterations / $elapsedSeconds);

    printf("%-24s %12s %14s\n", $label, number_format($iterations), number_format($rate));
}
