<?php

namespace App\Support;

use Andegna\Constants;
use Andegna\DateTime as EthDateTime;
use Andegna\DateTimeFactory;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Carbon as LaravelCarbon;

class EthiopianCalendar
{
    public static function timezone(): DateTimeZone
    {
        return new DateTimeZone(config('app.timezone', 'Africa/Addis_Ababa'));
    }

    public static function now(): EthDateTime
    {
        return new EthDateTime(new DateTime('now', self::timezone()));
    }

    public static function currentYear(): int
    {
        return self::now()->getYear();
    }

    public static function fromGregorian(Carbon|LaravelCarbon|string|null $value): ?EthDateTime
    {
        if ($value === null || $value === '') {
            return null;
        }

        $carbon = $value instanceof Carbon || $value instanceof LaravelCarbon
            ? $value->copy()->timezone(self::timezone())
            : LaravelCarbon::parse($value, self::timezone());

        return new EthDateTime($carbon->toDateTime());
    }

    public static function formatDate(Carbon|LaravelCarbon|string|null $value, bool $includeWeekDay = false): ?string
    {
        $ethiopian = self::fromGregorian($value);

        if ($ethiopian === null) {
            return null;
        }

        if ($includeWeekDay) {
            return $ethiopian->format('l፣ d F Y');
        }

        return sprintf(
            '%d %s %d',
            $ethiopian->getDay(),
            Constants::MONTHS_NAME[$ethiopian->getMonth()],
            $ethiopian->getYear(),
        );
    }

    public static function formatDateTime(Carbon|LaravelCarbon|string|null $value): ?string
    {
        $ethiopian = self::fromGregorian($value);

        if ($ethiopian === null) {
            return null;
        }

        return $ethiopian->format('l፣ F d ቀን H:i:s a Y E');
    }

    public static function formatTime(?string $time): ?string
    {
        if ($time === null || $time === '') {
            return null;
        }

        $parts = explode(':', substr($time, 0, 8));
        $hour = (int) ($parts[0] ?? 0);
        $minute = (int) ($parts[1] ?? 0);

        $ethiopianHour = ($hour - 6 + 24) % 12;

        if ($ethiopianHour === 0) {
            $ethiopianHour = 12;
        }

        $period = ($hour >= 6 && $hour < 18) ? 'ቀን' : 'ማታ';

        return sprintf('%s %d:%02d', $period, $ethiopianHour, $minute);
    }

    public static function formatClock(Carbon|LaravelCarbon|null $value = null): string
    {
        $carbon = ($value ?? now())->timezone(self::timezone());
        $date = self::formatDate($carbon, true) ?? '';
        $time = self::formatTime($carbon->format('H:i')) ?? '';

        return trim("{$date} · {$time}");
    }

    /**
     * @return array{start: LaravelCarbon, end: LaravelCarbon, daysInMonth: int}
     */
    public static function monthGregorianRange(int $year, int $month): array
    {
        $timezone = self::timezone();
        $startEthiopian = DateTimeFactory::of($year, $month, 1, 0, 0, 0, $timezone);
        $daysInMonth = $startEthiopian->getDaysInMonth();
        $endEthiopian = DateTimeFactory::of($year, $month, $daysInMonth, 23, 59, 59, $timezone);

        return [
            'start' => LaravelCarbon::instance($startEthiopian->toGregorian())->startOfDay(),
            'end' => LaravelCarbon::instance($endEthiopian->toGregorian())->endOfDay(),
            'daysInMonth' => $daysInMonth,
        ];
    }

    public static function formatShortDate(EthDateTime $ethiopian): string
    {
        return sprintf(
            '%02d/%02d/%04d',
            $ethiopian->getDay(),
            $ethiopian->getMonth(),
            $ethiopian->getYear(),
        );
    }

    public static function monthReportTitle(int $year, int $month): string
    {
        $timezone = self::timezone();
        $range = self::monthGregorianRange($year, $month);
        $startEthiopian = DateTimeFactory::of($year, $month, 1, 0, 0, 0, $timezone);
        $endEthiopian = DateTimeFactory::of($year, $month, $range['daysInMonth'], 0, 0, 0, $timezone);

        return sprintf(
            'ከ %s ዓ.ም እስከ %s ዓ.ም የወሩ ወደ ማረሚያ ቤት ገቡ ታራሚዎች እና የቀጠሮ እስረኞች',
            self::formatShortDate($startEthiopian),
            self::formatShortDate($endEthiopian),
        );
    }

    public static function educationAgeReportTitle(int $year, int $month): string
    {
        $monthName = Constants::MONTHS_NAME[$month] ?? (string) $month;

        return sprintf(
            'በ %s ውስጥ የ%s %d ዓ.ም የሚገኙ ሕግ ታራሚዎች ትምህርትና ዕድሜአቸውን የሚገልጽ ሠንጠረዥ',
            __('app.institute'),
            $monthName,
            $year,
        );
    }

    public static function sentenceTypeReportTitle(int $year, int $month): string
    {
        $monthName = Constants::MONTHS_NAME[$month] ?? (string) $month;

        return sprintf(
            'በ%s %d ዓ.ም ወር መጨረሻ የሚገኙ የህግ ታራሚዎች በፍርድ ልክ ስታቲስቲክስ መረጃ መላኪያ %s',
            $monthName,
            $year,
            config('sentence_type_report.form_code', 'ቅፅ - 1'),
        );
    }

    public static function newIntakeReportTitle(int $year, int $month): string
    {
        $monthName = Constants::MONTHS_NAME[$month] ?? (string) $month;

        return sprintf(
            'በ%s ውስጥ የ%s %d ዓ.ም የገቡ ታራሚዎች እና የቀጠሮ እስረኞች ስታትስቲክስ መረጃ የሚገልጽ ሠንጠረዥ %s',
            config('new_intake_report.institution', 'በአ/ምንጭ ማረሚያ ተቋም'),
            $monthName,
            $year,
            config('new_intake_report.form_code', 'ቅፅ - 3'),
        );
    }

    public static function releasedReportTitle(int $year, int $month): string
    {
        $monthName = Constants::MONTHS_NAME[$month] ?? (string) $month;

        return sprintf(
            'በክልሉ ማረሚያ ቤቶች የ%s %d ዓ.ም የወጡ ታራሚዎች እና የቀጠሮ እስረኞች ስታቲስቲክ መረጃ መላኪያ %s',
            $monthName,
            $year,
            config('released_report.form_code', 'ቅፅ-4'),
        );
    }

    /**
     * @return array{day: int, month: int, year: int}|null
     */
    public static function dateParts(Carbon|LaravelCarbon|string|null $value): ?array
    {
        $ethiopian = self::fromGregorian($value);

        if ($ethiopian === null) {
            return null;
        }

        return [
            'day' => $ethiopian->getDay(),
            'month' => $ethiopian->getMonth(),
            'year' => $ethiopian->getYear(),
        ];
    }

    public static function under18ReportTitle(int $year, int $month): string
    {
        $monthName = Constants::MONTHS_NAME[$month] ?? (string) $month;

        return sprintf(
            '%s የ%s %d ዓ.ም ከ18 ዓመት በታች የሆኑ የህግ ታራሚዎች ብዛት የሚገልፅ ሰንጠረዥ',
            config('under_18_report.institution_short', 'አ/ምንጭ ማ/ተቋም'),
            $monthName,
            $year,
        );
    }

    /**
     * @return array{day: string, month: string, year: string}
     */
    public static function durationParts(Carbon|LaravelCarbon $start, Carbon|LaravelCarbon $end): array
    {
        $days = max(0, $start->diffInDays($end));
        $years = intdiv($days, 360);
        $days %= 360;
        $months = intdiv($days, 30);
        $days %= 30;

        return [
            'day' => $days > 0 ? (string) $days : '-',
            'month' => $months > 0 ? (string) $months : '-',
            'year' => $years > 0 ? (string) $years : '-',
        ];
    }

    /**
     * @param  array{day: string, month: string, year: string}  $parts
     */
    public static function formatDurationLabel(array $parts): string
    {
        $year = $parts['year'] !== '-' && $parts['year'] !== '' ? $parts['year'] : null;
        $month = $parts['month'] !== '-' && $parts['month'] !== '' ? $parts['month'] : null;
        $day = $parts['day'] !== '-' && $parts['day'] !== '' ? $parts['day'] : null;

        if ($year !== null && $month !== null) {
            return $year.' ዓመት ከ'.$month.' ወር';
        }

        if ($year !== null) {
            return $year.' ዓመት';
        }

        if ($month !== null && $day !== null) {
            return $month.' ወር ከ'.$day.' ቀን';
        }

        if ($month !== null) {
            return $month.' ወር';
        }

        if ($day !== null) {
            return $day.' ቀን';
        }

        return '';
    }

    public static function paroleReleasedReportTitle(int $year, int $month): string
    {
        $monthName = Constants::MONTHS_NAME[$month] ?? (string) $month;
        $daysInMonth = self::monthGregorianRange($year, $month)['daysInMonth'];

        return sprintf(
            'በ%s ማ/ተቋም ከ%s 1 %d ዓ.ም እስከ %s %d %d ዓ.ም በአመክሮ የተፈቱ የሕግ ታራሚዎች ዝርዝር ስም',
            config('parole_released_report.institution_short', 'አ/ምንጭ'),
            $monthName,
            $year,
            $monthName,
            $daysInMonth,
            $year,
        );
    }

    public static function childrenWithMotherReportTitle(int $year, int $month): string
    {
        $monthName = Constants::MONTHS_NAME[$month] ?? (string) $month;

        return sprintf(
            'በ%s ማ/ተቋም ውስጥ የሚገኙ የ%s %d ዓ.ም ከእናቶቻቸው ጋር ያሉ ህፃናት ስም ዝርዝር',
            config('children_with_mother_report.institution_short', 'ኢ/ም/ጥ'),
            $monthName,
            $year,
        );
    }

    public static function deathSentencedReportTitle(int $year, int $month): string
    {
        $monthName = Constants::MONTHS_NAME[$month] ?? (string) $month;

        return sprintf(
            'የ%s %d ዓ.ም የሞት ፍርድ ተፈርዶባቸው በ%s ማ/ተቋም የሚገኙ ታራሚዎች ስታቲስቲክስ መረጃ %s',
            $monthName,
            $year,
            config('death_sentenced_report.institution_short', 'አ/ምንጭ'),
            config('death_sentenced_report.form_code', 'ቅፅ - 6'),
        );
    }

    public static function recidivistReportTitle(int $year, int $month): string
    {
        $monthName = Constants::MONTHS_NAME[$month] ?? (string) $month;

        return sprintf(
            'የ%s ወር %d ዓ.ም %s የገቡ ተመላላሽ የሕግ ታራሚዎች ስም ዝርዝር',
            $monthName,
            $year,
            config('recidivist_report.institution_short', 'ስ/ም/ማ/ተቋም'),
        );
    }

    /**
     * @return array<int, string>
     */
    public static function monthNames(): array
    {
        return Constants::MONTHS_NAME;
    }
}
