<?php
/** @noinspection PhpUndefinedVariableInspection */

namespace Calendars\Month;

use Calendars\Template;

class MonthTemplate extends Template
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
                        $this->img($value[$a]['img'], $this->style->user['img']['span']) . $this->span(
                            $value[$a]['name'],
                            $this->style->user['user']['span']
                        ) . $this->span($value[$a]['description'], $this->style->user['user']['span']),
                        $this->style->user['user']['div']
                    );
                }
            }
        }
        $e = 0;
        $month = '';
        $user = $this->div($user, $this->style->user['div']);
        $week = $this->span('Sem', $this->style->month['week']['span']);
        $divEvent = [];
        for ($a = 0; $a < $this->daysOfTheMonthInTheYear[substr($this->dates[0], 5, 2) - 1]; $a++) {
            if ($a < 7) {
                $week .= $this->span($this->week[$a], $this->style->month['week']['span']);
            }
            $activePlus = false;
            $plus = '';
            $key = [$this->amountYear[substr($this->dates[0], 0, 4)], $this->dates[$a], $this->times[$a][0]];
            $holiday = '';
            if (!empty($this->events[$a][implode(' ', $key)])) {
                $holiday = $this->span($this->events[$a][implode(' ', $key)], $this->style->month['holiday']['span']);
            }
            unset($this->times[$a][0], $this->events[$a][implode(' ', $key)]);
            $this->times[$a] = $this->helper->ksort($this->times[$a]);
            $divClassEvent = $this->style->month['event']['div']['class'];
            $event = '';
            $newEvent = [];
            for ($b = 0; $b < count($this->times[$a]); $b++) {
                $key[1] = $this->dates[$a];
                $key[2] = $this->times[$a][$b];
                foreach ($this->events[$a][implode(' ', $key)] as $value) {
                    if (count($value) > 0) {
                        $key[1] = $this->helper->countCurrentDays($key[1], $this->daysOfTheMonthInTheYear);
                        if (isset($keyUsers) && implode(' ', $key) == $keyUsers) {
                            $event .= $this->span(
                                $key[2] . ' - ' . $value[$keyUser],
                                $this->style->month['event']['span']
                            );
                        } else {
                            for ($c = 0; $c < count($value); $c++) {
                                $event .= $this->span($value[$c], $this->style->month['event']['span']);
                                if ($c == 5) {
                                    $activePlus = true;
                                } elseif ($c > 5) {
                                    $divEvent[] = $this->span($value[$c], $this->style->month['event']['span']);
                                }
                            }
                            $newEvent[] = $this->div(
                                $this->span($key[2], $this->style->month['time']['span']) . $this->div(
                                    $event,
                                    $this->style->month['event']['div']
                                ),
                                $this->style->month['time']['div']
                            );
                        }
                    }
                }
            }
            $key[1] = implode('/', array_reverse(explode('-', $this->dates[$a])));
            if (isset($divEvent) && !empty($event)) {
                $this->style->month['event']['div']['class'] = $divClassEvent . ' divHidden' . ($a < 10 ? '0' . $a : $a);
                $eventExtra[] = $this->div($event . implode('', $divEvent), $this->style->month['event']['div']);
                $divEvent = [];
            }
            if ($activePlus) {
                $plus = $this->span('', ['class' => 'icon icon-plus']);
            }
            if ((($a / 7) == $e)) {
                $month .= $this->span($this->weeks + $e, $this->style->month['week']['span']);
                $e++;
            }
            if ($a == 0 && date('w', strtotime($this->dates[$a])) != 0) {
                $days = '-' . date('w', strtotime($this->dates[$a]));
                for ($d = $days; $d < 0; $d++) {
                    $month .= $this->span('', $this->style->month['day']['span']);
                }
            }
            $month .= $this->span($key[1], $this->style->month['day']['span']) . $this->div(
                    $holiday . implode('', $newEvent) . $plus,
                    $this->style->month['event']['div']
                );
            if ($a == (count($this->dates) - 1) && date('w', strtotime($this->dates[$a])) != 6) {
                $days = 6 - date('w', strtotime($this->dates[$a]));
                for ($d = 0; $d < $days; $d++) {
                    $month .= $this->span('', $this->style->month['day']['span']);
                }
            }
        }
        $month = $this->div(
            $this->displayMode(
                $this->date,
                $this->style->month['display']
            ) . $this->div(
                $this->div(
                    $this->month,
                    $this->style->month['identify']['div']
                ) . $this->div(
                    $week,
                    $this->style->month['week']['div']
                ) . $this->div(
                    $month,
                    $this->style->month['day']['div']
                ),
                $this->style->month['subdiv']
            ),
            $this->style->month['div']
        );
        $styleCalendar = $this->style->calendar['year']['span']['style'];
        $this->style->calendar['year']['span']['style'] .= 'font-weight:bold;';
        $year = $this->span('<', $this->style->calendar['year']['span']);
        $this->style->calendar['year']['span']['style'] = $styleCalendar;
        $year .= $this->span(array_search($key[0], $this->amountYear), $this->style->calendar['year']['span']);
        $this->style->calendar['year']['span']['style'] .= 'font-weight:bold;';
        $year .= $this->span('>', $this->style->calendar['year']['span']);
        $calendar = '';
        for ($a = 0; $a < count($this->months); $a++) {
            $calendar .= $this->span($this->months[$a], $this->style->calendar['months']['span']);
        }
        $calendar = $this->div(
            $this->div($year, $this->style->calendar['year']['div']) . $this->div(
                $calendar,
                $this->style->calendar['months']['div']
            ),
            $this->style->calendar['div']
        );
        return $this->htmlTemplate($user . $month . $calendar . implode('', $divEvent), __CLASS__);
    }
}