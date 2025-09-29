<?php
/** @noinspection ALL */


namespace Calendars\callback;

use Calendars\Template;

class CallbackTemplate extends Template
{
    public function render(string $identifier = 'month'): string
    {
        if ($identifier == 'year' || $identifier == 'years') {
            for ($a = 0; $a < count($this->years); $a++) {
                $dates = explode(' - ', $this->years[$a]);
                if ($dates[0] <= $this->year && $dates[1] >= $this->year) {
                    $key = $a;
                }
            }
        }
        $calendar = '';
        if ($identifier == 'month') {
            $monthKey = array_search($this->month, $this->months);
            $year = substr($this->date['select'], 0, 5);
            $day = substr($this->date['select'], 7);
            $this->style->calendar['identifier']['span']['prev']['link']['options'] = [
                'data-callback' => $year . (($monthKey - 1) < 10 ? '0' . ($monthKey - 1) : ($monthKey - 1)) . $day,
                'data-method' => 'month',
                'class' => 'callback'
            ];
            $this->style->calendar['identifier']['span']['next']['link']['options'] = [
                'data-callback' => $year . (($monthKey + 1) < 10 ? '0' . ($monthKey + 1) : ($monthKey + 1)) . $day,
                'data-method' => 'month',
                'class' => 'callback'
            ];
            $this->style->calendar['identifier']['div']['class'] .= '-month';
            $this->style->calendar['identifier']['span']['identification']['link']['options'] = [
                'data-callback' => $this->year . substr($this->date['select'], 4),
                'data-method' => 'months',
                'class' => 'callback'
            ];
            $identificator = substr($this->month, 0, 3);
            $calendar = $this->days($calendar);
        } elseif ($identifier == 'year') {
            $years = explode('-', $this->years[$key]);
            $dates = trim($years[0]) . substr($this->date['select'], 4) . ' - ' . trim($years[1]) . substr(
                    $this->date['select'],
                    4
                );
            $this->style->calendar['identifier']['span']['prev']['link']['options'] = [
                'data-callback' => ($this->year - 1) . substr($this->date['select'], 4),
                'data-method' => 'year',
                'class' => 'callback'
            ];
            $this->style->calendar['identifier']['span']['next']['link']['options'] = [
                'data-callback' => ($this->year + 1) . substr($this->date['select'], 4),
                'data-method' => 'year',
                'class' => 'callback'
            ];
            $this->style->calendar['identifier']['div']['class'] .= '-year';
            $this->style->calendar['identifier']['span']['identification']['link']['options'] = [
                'data-callback' => $dates,
                'data-method' => 'years',
                'class' => 'callback'
            ];
            $identificator = $this->year;
            $calendar = $this->months($calendar);
        } elseif ($identifier == 'years') {
            $dates = explode(' - ', $this->years[($key - 1)]);
            $dates = $dates[0] . substr($this->date['select'], 4, 6) . ' - ' . $dates[1] . substr(
                    $this->date['select'],
                    4,
                    6
                );
            $this->style->calendar['identifier']['span']['prev']['link']['options'] = [
                'data-callback' => $dates,
                'data-method' => 'years',
                'class' => 'callback'
            ];
            $dates = explode(' - ', $this->years[($key + 1)]);
            $dates = $dates[0] . substr($this->date['select'], 4, 6) . ' - ' . $dates[1] . substr(
                    $this->date['select'],
                    4,
                    6
                );
            $this->style->calendar['identifier']['span']['next']['link']['options'] = [
                'data-callback' => $dates,
                'data-method' => 'years',
                'class' => 'callback'
            ];
            $identificator = $this->years[$key];
            unset($this->style->calendar['identifier']['span']['identification']['link']);
            $this->style->calendar['identifier']['div']['class'] .= "-years";
            $calendar = $this->years($calendar);
        }
        return $this->div(
                $this->span(
                    '<',
                    $this->style->calendar['identifier']['span']['prev']
                ) . $this->span(
                    $identificator,
                    $this->style->calendar['identifier']['span']['identification']
                ) . $this->span(
                    '>',
                    $this->style->calendar['identifier']['span']['next']
                ),
                $this->style->calendar['identifier']['div']
            ) . $calendar;
    }

    private function days(string $calendar): string
    {
        $week = $this->span('Sem');
        $weekDays = 0;
        $daysOfTheMonth = $this->daysOfTheMonthInTheYear[substr($this->date['select'], 5, 2) - 1];
        $b = 1;
        for ($a = 1; $a <= $daysOfTheMonth; $a++) {
            if ($a <= 7) {
                $week .= $this->span($this->week[$a - 1]);
                if ($a == 1) {
                    $day = $this->span($this->weeks);
                }
            }
            $number = $a < 10 ? "0" . $a : $a;
            $date = substr($this->date['select'], 0, 8) . $number;
            if (($a - 1) == 0 && date('w', strtotime($date)) != 0) {
                $days = '-' . date('w', strtotime($date));
                $daysLastMonth = $this->daysOfTheMonthInTheYear[substr($this->date['select'], 5, 2) - 2];
                for ($d = $days; $d < 0; $d++) {
                    $day .= $this->span(($daysLastMonth + (1 + $d)), ['class' => 'day-not-visible']);
                }
            }
            $span = $this->style->calendar['days']['span'];
            if ($date == $this->date['today'] || $date == $this->date['select']) {
                if ($this->cssSelectActive) {
                    if ($date == $this->date['select']) {
                        $span['class'] .= ' span-select';
                    }
                }
                if ($date == $this->date['today']) {
                    $span['class'] .= ' span-active';
                }
                if (stripos($span['class'], 'select') !== false && stripos($span['class'], 'active') !== false) {
                    $span['class'] = str_replace('span-select span-active', 'span-selectAndActive', $span['class']);
                }
            } else {
                $span['class'] .= '';
            }
            $span['link']['content'] = 'lists' . DS . $date;
            $span['link']['options'] = [
                'data-callback' => $date,
                'data-method' => 'day',
                'class' => 'callback'
            ];
            if (!empty($this->user['name'])) {
                $span['link']['content'] .= DS . $this->user['name'];
            }
            $day .= $this->span($number, $span);
            if ($a == $daysOfTheMonth && date('w', strtotime($date)) != 6) {
                $days = 6 - date('w', strtotime($date));
                for ($d = 1; $d <= $days; $d++) {
                    $day .= $this->span(($d < 10 ? '0' . $d : $d), ['class' => 'day-not-visible']);
                }
            }
            if (date('w', strtotime($date)) == 6) {
                $weekDays++;
                $calendar .= $this->div($day, $this->style->calendar['day']['div']);
                $day = $this->span($this->weeks + $b);
                $b++;
            }
        }
        if ($weekDays < 6) {
            $calendar .= $this->div($day, $this->style->calendar['day']['div']);
            $day = $this->span($this->weeks + $b);
            for ($a = 0; $a < 7; $a++) {
                $day .= $this->span(
                    (($d + $a) < 10 ? '0' . ($d + $a) : ($d + $a)),
                    ['class' => 'day-not-visible']
                );
            }
            $calendar .= $this->div($day, $this->style->calendar['day']['div']);
        }
        return $this->div(
                $week,
                $this->style->calendar['week']['div']
            ) . $this->div(
                $calendar,
                $this->style->calendar['days']['div']
            );
    }

    private function months(string $calendar): string
    {
        $this->style->calendar['month']['span']['link']['content'] = '#';
        $months = '';
        for ($a = 0; $a < count($this->months); $a++) {
            $span = $this->style->calendar['month']['span'];
            $date = substr(
                    $this->date['select'],
                    0,
                    5
                ) . (($a + 1) < 10 ? '0' . ($a + 1) : ($a + 1)) . substr($this->date['select'], 7);
            if ($date == $this->date['today'] || $date == $this->date['select']) {
                if ($this->cssSelectActive) {
                    if ($date == $this->date['select']) {
                        $span['class'] .= ' span-select';
                    }
                }
                if ($date == $this->date['today']) {
                    $span['class'] .= ' span-active';
                }
                if (stripos($span['class'], 'select') !== false && stripos($span['class'], 'active') !== false) {
                    $span['class'] = str_replace('span-select span-active', 'span-selectAndActive', $span['class']);
                }
            } else {
                if (in_array($a, [0, 4, 8]) !== false) {
                    $span['class'] .= ' primary-month';
                } elseif (in_array($a, [3, 7, 11]) !== false) {
                    $span['class'] .= ' four-month';
                } else {
                    $span['class'] .= '';
                }
            }
            $span['link']['options'] = [
                'data-callback' => $date,
                'data-method' => 'month',
                'class' => 'callback'
            ];
            $months .= $this->span(trim(substr($this->months[$a], 0, 3)), $span);
            if (in_array($a, [3, 7, 11]) !== false) {
                $calendar .= $this->div($months, $this->style->calendar['month']['div']);
                $months = '';
            }
        }
        return $this->div(
            $calendar,
            $this->style->calendar['months']['div']
        );
    }

    private function years(string $calendar): string
    {
        $years = '';
        for ($a = 0; $a < 10; $a++) {
            $span = $this->style->calendar['year']['span'];
            $date = substr($this->date['select'], 0, 3) . $a . substr($this->date['select'], 4, 6);
            if ($date == $this->date['today'] || $date == $this->date['select']) {
                if ($this->cssSelectActive) {
                    if ($date == $this->date['select']) {
                        $span['class'] .= ' span-select';
                    }
                }
                if ($date == $this->date['today']) {
                    $span['class'] .= ' span-active';
                }
                if (stripos($span['class'], 'select') !== false && stripos($span['class'], 'active') !== false) {
                    $span['class'] = str_replace('span-select span-active', 'span-selectAndActive', $span['class']);
                }
            } else {
                if (in_array($a, [0, 4]) !== false) {
                    $span['class'] .= ' primary-year';
                } elseif (in_array($a, [3, 7]) !== false) {
                    $span['class'] .= ' four-year';
                } elseif (in_array($a, [8, 9]) !== false) {
                    $span['class'] .= ' year-two';
                } else {
                    $span['class'] .= '';
                }
            }
            $span['link']['options'] = [
                'data-callback' => $date,
                'data-method' => 'year',
                'class' => 'callback'
            ];
            $years .= $this->span(substr($date, 0, 4), $span);
            if (in_array($a, [3, 7, 9]) !== false) {
                $calendar .= $this->div($years, $this->style->calendar['year']['div']);
                $years = '';
            }
        }
        return $this->div(
            $calendar,
            $this->style->calendar['years']['div']
        );
    }
}