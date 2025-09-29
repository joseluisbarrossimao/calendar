<?php
/** @noinspection PhpUndefinedVariableInspection */

namespace Calendars\Day;

use Calendars\Template;

class DayTemplate extends Template
{

    public function render(): string
    {
        $datas[] = $this->date;
        $user = '';
        $styleUserDiv = $this->style->user['user']['div']['class'];
        foreach ($this->users as $key => $value) {
            for ($a = 0; $a < count($value); $a++) {
                if (stripos($value[$a]['name'], $user) === false) {
                    if ($value[$a]['name'] == $this->user['name']) {
                        $keyUsers = $key;
                        $keyUser = $a;
                        $datas[] = str_replace(' ', '_', $value[$a]['name']);
                        $this->style->user['user']['div']['class'] = $styleUserDiv . ' user-active';
                    } else {
                        $this->style->user['user']['div']['class'] = $styleUserDiv . ' user-not-active';
                    }
                    $newUser = $this->img(
                            $value[$a]['img'],
                            $this->style->user['img']
                        ) . $this->span(
                            $value[$a]['name']
                        ) . $this->span(
                            $value[$a]['description']
                        );
                    $user .= $this->div(
                        $this->link(
                            $newUser,
                            'day' . DS . $this->date . DS . str_replace(' ', '_', $value[$a]['name']),
                            $this->style->user['link']
                        ),
                        $this->style->user['user']['div']
                    );
                }
            }
        }
        $user = $this->div($user, $this->style->user['div']);
        $key = [$this->amountYear[substr($this->dates[0], 0, 4)], $this->dates[0], $this->times[0]];
        $holiday = '';
        if (isset($this->events[implode(' ', $key)])) {
            $holiday = $this->span($this->events[implode(' ', $key)], $this->style->day['holiday']['span']);
        }
        unset($this->times[0], $this->events[implode(' ', $key)]);
        $this->times = $this->helper->ksort($this->times);
        for ($a = 0; $a < count($this->times); $a++) {
            $event = '';
            $key[1] = $this->dates[0];
            $key[2] = $this->times[$a];
            foreach ($this->events[implode(' ', $key)] as $value) {
                if (count($value) > 0) {
                    $key[1] = $this->helper->countCurrentDays($key[1], $this->daysOfTheMonthInTheYear);
                    if (isset($keyUsers) && implode(' ', $key) == $keyUsers) {
                        $event .= $this->span($value[$keyUser], $this->style->day['event']['span']);
                    } else {
                        for ($b = 0; $b < count($value); $b++) {
                            $event .= $this->span($value[$b], $this->style->day['event']['span']);
                        }
                    }
                }
            }
            $div[] = $this->div(
                $this->span($key[2], $this->style->day['time']['span']) . $this->div(
                    $event,
                    $this->style->day['event']['div']
                ),
                $this->style->day['time']['div']
            );
        }
        $day[] = $this->div(
            $this->span(
                implode("/", array_reverse(explode("-", $this->dates[0]))) . $this->week[date(
                    'w',
                    strtotime(
                        $this->dates[0]
                    )
                )],
                $this->style->day['day']['span']
            ) . $holiday . implode('', $div),
            $this->style->day['day']['div']
        );
        $day = $this->div(
            $this->displayMode(
                $datas,
                $this->style->day['display']
            ) . $this->div(
                $this->div(
                    $this->span($this->month, $this->style->day['identify']['span']),
                    $this->style->day['identify']['div']
                ) . implode('', $day),
                $this->style->day['subdiv']
            ),
            $this->style->day['div']
        );
        $styleCalendar = $this->style->calendar['month']['span']['style'];
        $this->style->calendar['month']['span']['style'] .= 'font-weight:bold;';
        $month = $this->span('<', $this->style->calendar['month']['span']);
        $this->style->calendar['month']['span']['style'] = $styleCalendar;
        $month .= $this->span($this->month, $this->style->calendar['month']['span']);
        $this->style->calendar['month']['span']['style'] .= 'font-weight:bold;';
        $month .= $this->span('>', $this->style->calendar['month']['span']);
        $calendar = '';
        $week = $this->span('Sem', $this->style->calendar['week']['span']);
        $daysOfTheMonth = $this->daysOfTheMonthInTheYear[substr(
            implode('/', array_reverse(explode('-', $this->date))),
            3,
            2
        ) - 1];
        $b = 1;
        for ($a = 1; $a <= $daysOfTheMonth; $a++) {
            if ($a <= 7) {
                $week .= $this->span($this->week[$a - 1], $this->style->calendar['week']['span']);
                if ($a == 1) {
                    $calendar .= $this->span($this->weeks, $this->style->calendar['week']['span']);
                }
            }
            $number = $a < 10 ? "0" . $a : $a;
            if (($a - 1) == 0 && date('w', strtotime(substr($this->date, 0, 8) . $number)) != 0) {
                $days = '-' . date('w', strtotime(substr($this->date, 0, 8) . $number));
                $span = $this->style->calendar['days']['span'];
                unset($span['link']);
                $span['style'] .= 'color:#ffffff;';
                for ($d = $days; $d < 0; $d++) {
                    $calendar .= $this->span('00', $span);
                }
            }
            $calendar .= $this->span($number, $this->style->calendar['days']['span']);
            if ($a == $daysOfTheMonth && date('w', strtotime(substr($this->date, 0, 8) . $number)) != 6) {
                $days = 6 - date('w', strtotime(substr($this->date, 0, 8) . $number));
                if (!isset($span)) {
                    $span = $this->style->calendar['days']['span'];
                    unset($span['link']);
                    $span['style'] .= 'color:#ffffff;';
                }
                for ($d = 0; $d < $days; $d++) {
                    $calendar .= $this->span('00', $span);
                }
            }
            if (date('w', strtotime(substr($this->date, 0, 8) . $number)) == 6) {
                $calendar .= $this->span($this->weeks + $b, $this->style->calendar['week']['span']);
                $b++;
            }
        }
        $calendar = $this->div(
            $this->div($month, $this->style->calendar['month']['div']) . $this->div(
                $week,
                $this->style->calendar['week']['div']
            ) . $this->div(
                $calendar,
                $this->style->calendar['days']['div']
            ),
            $this->style->calendar['div']
        );
        return $this->htmlTemplate($user . $day . $calendar, __CLASS__);
    }
}