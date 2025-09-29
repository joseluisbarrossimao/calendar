<?php
/** @noinspection PhpUndefinedVariableInspection */

namespace Calendars\Week;

use Calendars\Template;

class WeekTemplate extends Template
{

    public function render(): string
    {
        $user = '';
        $styleUserSpan = $this->style->user['user']['span']['style'];
        $styleUserDiv = $this->style->user['user']['div']['style'];
        $styleImg = $this->style->user['img']['span']['style'];
        foreach ($this->users as $key => $value) {
            for ($a = 0; $a < count($value); $a++) {
                if (stripos($value[$a]['name'], $user) === false) {
                    if ($value[$a]['name'] == $this->user['name']) {
                        $keyUsers = $key;
                        $keyUser = $a;
                        $this->style->user['user']['span']['style'] = $styleUserSpan;
                        $this->style->user['user']['div']['style'] .= 'border:2px solid red;border-radius:5px;';
                        $this->style->user['img']['span']['style'] = $styleImg;
                    } else {
                        if (stripos($this->style->user['user']['span']['style'], 'color') === false) {
                            $this->style->user['user']['span']['style'] .= 'color:#6c757d;';
                            $this->style->user['img']['span']['style'] .= 'filter:opacity(50%);';
                        }
                        if (stripos($this->style->user['user']['div']['style'], 'border') !== false) {
                            $this->style->user['user']['div']['style'] = $styleUserDiv;
                        }
                    }
                    $user .= $this->div(
                        $this->img(
                            $value[$a]['img'],
                            $this->style->user['img']['span']
                        ) . $this->span(
                            $value[$a]['name'],
                            $this->style->user['user']['span']
                        ) . $this->span(
                            $value[$a]['description'],
                            $this->style->user['user']['span']
                        ),
                        $this->style->user['user']['div']
                    );
                }
            }
        }
        $user = $this->div($user, $this->style->user['div']);
        for ($a = 0; $a < count($this->dates); $a++) {
            $key = [$this->amountYear[substr($this->dates[0], 0, 4)], $this->dates[0], $this->times[$a][0]];
            $holiday = '';
            if (isset($this->events[$a][implode(' ', $key)])) {
                $holiday = $this->span($this->events[$a][implode(' ', $key)], $this->style->week['holiday']['span']);
            }
            unset($this->times[$a][0], $this->events[$a][implode(' ', $key)]);
            $this->times[$a] = $this->helper->ksort($this->times[$a]);
            for ($b = 0; $b < count($this->times[$a]); $b++) {
                $event = '';
                $key[1] = $this->dates[$a];
                $key[2] = $this->times[$a][$b];
                foreach ($this->events[$a][implode(' ', $key)] as $value) {
                    if (count($value) > 0) {
                        $key[1] = $this->helper->countCurrentDays($key[1], $this->daysOfTheMonthInTheYear);
                        if (isset($keyUsers) && implode(' ', $key) == $keyUsers) {
                            $event .= $this->span($value[$keyUser], $this->style->week['event']['span']);
                        } else {
                            for ($c = 0; $c < count($value); $c++) {
                                $event .= $this->span($value[$c], $this->style->week['event']['span']);
                            }
                        }
                    }
                }
                $div[] = $this->div(
                    $this->span($key[2], $this->style->week['time']['span']) . $this->div(
                        $event,
                        $this->style->week['event']['div']
                    ),
                    $this->style->week['time']['div']
                );
            }
            $days[$a] = $this->div(
                $this->span(
                    implode("/", array_reverse(explode("-", $this->dates[$a]))) . $this->week[date(
                        'w',
                        strtotime(
                            $this->dates[$a]
                        )
                    )],
                    $this->style->week['day']['span']
                ) . $holiday . implode('', $div),
                $this->style->week['day']['div']
            );
        }
        $weeks = $this->div(
            $this->displayMode(
                $this->date,
                $this->style->week['display']
            ) . $this->div(
                $this->div(
                    $this->span($this->month, $this->style->week['identify']['span']),
                    $this->style->week['identify']['div']
                ) . implode('', $days),
                $this->style->week['subdiv']
            ),
            $this->style->week['div']
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
            $this->div(
                $month,
                $this->style->calendar['month']['div']
            ) . $this->div(
                $week,
                $this->style->calendar['week']['div']
            ) . $this->div(
                $calendar,
                $this->style->calendar['days']['div']
            ),
            $this->style->calendar['div']
        );
        return $this->htmlTemplate($user . $weeks . $calendar, __CLASS__);
    }
}