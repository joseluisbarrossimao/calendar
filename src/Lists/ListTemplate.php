<?php
/** @noinspection PhpUndefinedVariableInspection */


namespace Calendars\Lists;

use Calendars\Template;

/**
 * Class ListTemplate
 * @package Calendars\Lists
 */
class ListTemplate extends Template
{

    /**
     * @return string
     */
    public function render(): string
    {
        $selectedUser = true;
        foreach ($this->users as $key => $value) {
            if ($key == $this->user['name']) {
                $valueUsers = $value;
                $colorUser = $this->datasUsers[$key]['color'];
                $selectedUser = false;
            }
            $colorUsers[] = $this->datasUsers[$key]['color'];
        }
        $days = [];
        for ($a = 0; $a < count($this->dates); $a++) {
            $insertDayToList = true;
            $key = [$this->amountYear[substr($this->dates[$a], 0, 4)], $this->dates[$a], $this->times[$a][0]];
            $holiday = '';
            if (!empty($this->events[$a]['holiday'][implode(' ', $key)])) {
                $holiday = $this->disabledHoliday(
                    'Dia Todo',
                    $this->events[$a]['holiday'][implode(' ', $key)],
                    $this->style->lists['holiday']
                );
            }
            if (isset($this->events[$a]['normal'])) {
                $this->events[$a] = $this->events[$a]['normal'];
            }
            $events = '';
            unset($this->times[$a][0]);
            $this->times[$a] = $this->helper->ksort($this->times[$a]);
            for ($b = 0; $b < count($this->times[$a]); $b++) {
                $countEvent = 0;
                $time = '';
                $clearTime = false;
                $event = [];
                $key[1] = $this->dates[$a];
                $key[2] = substr($this->times[$a][$b], 0, stripos($this->times[$a][$b], ' até '));
                foreach ($this->events[$a][implode(' ', $key)] as $values) {
                    if (count($values) > 0) {
                        $countEvent = count($values);
                        $key[1] = $this->helper->countCurrentDays($key[1], $this->daysOfTheMonthInTheYear);
                        for ($c = 0; $c < count($values); $c++) {
                            $newValue = explode(' - ', $values[$c]);
                            if (in_array($newValue[1], $colorUsers) !== false) {
                                $dif = $this->extra[$this->dates[$a]][$newValue[1]];
                                $text = $this->allDay[$this->dates[$a]][$newValue[1]] ? 'Dia Todo' : 'dif. de ' . $dif['data-dateDiff'] . ' dias, ' . $this->times[$a][$b];
                                if ($this->validNotExistsEvent($text, $event)) {
                                    if ($this->notDoubledTime($text, $a)) {
                                        $clearTime = true;
                                        $this->style->lists['time']['span']['class'] .= ' multi';
                                    } else {
                                        if (count($event) > 0 && stripos(
                                                $event[count($event) - 1],
                                                ' multi'
                                            ) !== false) {
                                            $newText = $event[count($event) - 1];
                                            $event[count($event) - 1] = str_replace(' multi', '', $newText);
                                            $this->style->lists['time']['span']['class'] = str_replace(
                                                ' multi',
                                                '',
                                                $this->style->lists['time']['span']['class;']
                                            );
                                        }
                                    }
                                    $time = $this->span($text, $this->style->lists['time']['span']);
                                }
                                $this->style->lists['event']['span']['style'] = 'background-color:' . $newValue[1] . ';';
                                $event[] = $time . $this->span(
                                        $newValue[0],
                                        array_merge($this->style->lists['event']['span'], $dif)
                                    );
                                if (isset($colorUser) && $colorUser === $newValue[1]) {
                                    $event = [];
                                    $insertDayToList = true;
                                    $event[] = $time . $this->span(
                                            $newValue[0],
                                            array_merge($this->style->lists['event']['span'], $dif)
                                        );
                                    break;
                                }
                                if ($clearTime) {
                                    $time = '';
                                    $clearTime = false;
                                }
                            }
                        }
                    }
                    if (!$selectedUser && isset($valueUsers) && in_array(implode(' ', $key), $valueUsers) === false) {
                        $insertDayToList = false;
                    }
                }
                if ($insertDayToList) {
                    $events .= $this->div(
                        implode('', $event),
                        array_merge(
                            $this->style->lists['event']['div'],
                            ['style' => 'height:' . (28.5 * $countEvent) . 'px']
                        )
                    );
                }
            }
            $key[1] = $this->dates[$a];
            if (!empty($events) || !empty($holiday)) {
                $days[substr($key[1], 0, 4)][] = $this->div(
                    $this->div(
                        $this->span(
                            implode('/', array_reverse(explode('-', $key[1]))),
                            $this->style->lists['date']['span']
                        ) . $this->span(
                            ", " . $this->week[date('w', strtotime($key[1]))] . ": ",
                            $this->style->lists['week']['span']
                        ),
                        $this->style->lists['date']['div']
                    ) . $this->disabledMoonPhases(
                        $this->moonPhases[$a],
                        $this->style->lists['moonPhases']
                    ) . $holiday . $events,
                    $this->style->lists['day']['div']
                );
            }
        }
        $lists = '';
        foreach ($days as $key => $value) {
            $lists .= $this->div(
                $this->span($key, $this->style->lists['year']['span']) . implode('', $value),
                $this->style->lists['year']['div']
            );
        }
        return $this->div(
            $lists,
            $this->style->lists['div']
        );
    }

    public function validNotExistsEvent(string $time, array $events): bool
    {
        $count = 0;
        if (count($events) > 0) {
            foreach ($events as $event) {
                if (stripos($event, $time) !== false) {
                    $count++;
                }
            }
            if ($count == 0) {
                return true;
            }
            return false;
        }
        return true;
    }

    private function notDoubledTime(string $text, int $key): bool
    {
        $count = 0;
        if (in_array($text, $this->moreThanOneEventAtTheSameTime[$key]) !== false) {
            foreach ($this->moreThanOneEventAtTheSameTime[$key] as $time) {
                if ($time == $text) {
                    $count++;
                }
            }
            if ($count > 1) {
                return true;
            }
        }
        return false;
    }
}