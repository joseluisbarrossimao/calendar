<?php
/** @noinspection ALL */

/** @noinspection ALL */


namespace Calendars;


/**
 * Class Helper
 * @package Calendars
 */
class Helper
{

    /**
     * @var string
     */
    private $year = '';

    /**
     * @var bool
     */
    private $holidays;

    /**
     * @var bool
     */
    private $moonPhases;

    /**
     * @var bool
     */
    private $displayMode = true;

    /**
     * @var string[]
     */
    private $thows = [
        'dates' => 'This data of the data key does not contain the key with the initial name in the data key.',
        'user' => 'This data cannot be empty in the user key.'
    ];

    /**
     * Helper constructor.
     * @param bool $holidays
     * @param bool $displayMode
     */
    public function __construct(bool $holidays, bool $displayMode, bool $moonPhases)
    {
        $this->holidays = $holidays;
        $this->moonPhases = $moonPhases;
        $this->displayMode = $displayMode;
        return $this;
    }

    /**
     * @param string $year
     * @return $this
     */
    public function year(string $year): Helper
    {
        $this->year = $year;
        return $this;
    }

    /**
     * @return bool
     */
    public function createTheDisplayModeInTheView(): bool
    {
        return $this->displayMode;
    }

    /**
     * @return mixed
     */
    public function createTheExtraInTheView(string $method = null)
    {
        if (isset($method)) {
            return $this->holidays;
        }
        return ['holiday' => $this->holidays, 'moonPhases' => $this->moonPhases];
    }

    /**
     * @param array $datas
     * @param array $holidays
     * @param array $dateExecute
     * @return array
     */
    public function appearEventThatHasAHoliday(array $datas, array $holidays, array $dateExecute): array
    {
        if ($this->holidays && count($holidays) > 0) {
            if (count($dateExecute) > 0) {
                $length = 10;
                if ($dateExecute['type'] == 'month') {
                    $length = 8;
                } elseif ($dateExecute['type'] == 'year') {
                    $length = 4;
                }
                foreach ($holidays as $value) {
                    if (substr($value['date'], 0, $length) == substr($dateExecute['date'], 0, $length)) {
                        $newDate[] = $value['date'];
                        $newName[] = $value['name'];
                    }
                }
            } else {
                $newDate = $holidays;
            }
            if (isset($newDate)) {
                $keys = array_keys($datas['event']);
                for ($a = 0; $a < count($datas['dates']); $a++) {
                    if (in_array($datas['dates'][$a], $newDate) !== false) {
                        foreach ($datas['event'][$keys[$a]] as $key => $value) {
                            if (in_array($value, $newName) === false) {
                                unset($datas['event'][$keys[$a]][$key]);
                            }
                        }
                        if (count($datas['time'][$a]) > 1) {
                            foreach (array_keys($datas['time'][$a]) as $key) {
                                if ($key != 0) {
                                    unset($datas['time'][$a][$key]);
                                }
                            }
                        }
                        $datas['time'][$a][0] = '';
                    }
                }
            }
        }
        $datas = $this->ksort($datas, true);
        return $datas;
    }

    /**
     * @param array $datas
     * @param bool $keyName
     * @return array
     */
    public function ksort(array $datas, bool $keyName = false): array
    {
        $newDatas = [];
        if ($keyName) {
            $keys = ['time', 'event', 'dates'];
            for ($a = 0; $a < count($keys); $a++) {
                $newKey = 0;
                $nameKey = $keys[$a];
                if ($nameKey == 'time') {
                    foreach ($datas[$nameKey] as $key => $value) {
                        $newDataKey = $nameKey == 'time' ? array_search(
                            $key,
                            array_keys($datas['dates'])
                        ) : array_search($value['date'], $datas['dates']);
                        $newDatas[$a][$newDataKey] = $value;
                    }
                } else {
                    foreach ($datas[$nameKey] as $value) {
                        $newDatas[$a][$newKey] = $value;
                        $newKey++;
                    }
                }
            }
            return $newDatas;
        }
        $key = 0;
        foreach ($datas as $value) {
            $newDatas[$key] = $value;
            $key++;
        }
        return $newDatas;
    }

    /**
     * @param array $event
     */
    public function vadaleting(array $event): void
    {
        $result = $this->validate($event);
        if (!empty($result)) {
            throw new Exception($result);
        }
        $result = $this->validate($event, 'dates');
        if (!empty($result)) {
            throw new Exception($result);
        }
        $result = $this->validate($event, 'user');
        if (!empty($result)) {
            throw new Exception($result);
        }
        $result = $this->validate($event, 'extra');
        if (!empty($result)) {
            throw new Exception($result);
        }
        return;
    }

    /**
     * @param array $data
     * @param string $string
     * @return string
     */
    private function validate(array $data, string $string = ''): string
    {
        $result = 0;
        if (empty($string)) {
            $keys = ['dates', 'times', 'event', 'user', 'extra', 'allDay'];
            $a = array_keys($data);
            foreach (array_keys($data) as $key) {
                if (in_array($key, $keys) === false) {
                    $result++;
                }
            }
            if ($result > 0) {
                return 'These data do not contain any of these necessary keys, these keys are: date, event, time, user, extra and all Day.';
            }
            return '';
        }
        $newData = $data[$string];
        if ($string == 'date') {
            if (array_key_exists('initial', $newData) === false) {
                return $this->thows[$string];
            }
            if (!preg_match("/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/i", $newData['initial'])) {
                return 'This date is not in the format required by the database (ex:2012-07-10).';
            }
        } elseif (empty($newData)) {
            return $this->thows[$string];
        }
        if ($string == 'extra') {
            if (array_key_exists('idQuery', $newData) === false) {
                return 'These data do not contain any of these necessary key idQuery.';
            }
        }
        return '';
    }

    /**
     * @param string $date
     * @param array $dayMonth
     * @return string
     */
    public function countCurrentDays(string $date, array $dayMonth): string
    {
        if (strpos($date, "/") !== false) {
            $date = implode("-", array_reverse(explode("/", $date)));
        }
        $count = 0;
        if (substr($date, 0, 4) == "1970") {
            return $count;
        }
        for ($a = 0; $a < (substr($date, 5, 2) - 1); $a++) {
            $count += $dayMonth[$a];
        }
        $count += substr($date, 8);
        return $count;
    }

    /**
     * @param string $date
     * @param array $dayMonth
     * @return string
     */
    public function TotalCurrentDays(string $date, array $dayMonth): string
    {
        $count = 0;
        for ($a = -30; $a < 29; $a++) {
            $daysOfTheMonth = $dayMonth[$this->year + $a];
            for ($b = 0; $b < count($daysOfTheMonth); $b++) {
                $count += $daysOfTheMonth[$b];
            }
        }
        return $count;
    }

    /**
     * @param array $datas
     * @param array $amountYears
     * @return array
     */
    public function orderDatas(array $datas, array $amountYears): array
    {
        $oldAllDay = $datas['allDay'];
        $oldDates = $datas['dates'];
        $oldTimes = $datas['times'];
        $oldEvents = $datas['events'];
        foreach ($datas['dates'] as $date) {
            $newAmountYears[substr($date, 0, 4)] = $amountYears[substr($date, 0, 4)];
            $newDates[] = strtotime($date);
        }
        array_unique($newAmountYears);
        sort($newDates);
        $datas['dates'] = [];
        $datas['allDay'] = [];
        $datas['events'] = [];
        $datas['times'] = [];
        foreach ($newDates as $date) {
            $datas['dates'][] = date("d/m/Y", $date);
        }
        for ($a = 0; $a < count($datas['dates']); $a++) {
            $date = implode("-", array_reverse(explode("/", $datas['dates'][$a])));
            $datas['times'][$a] = $oldTimes[array_search($date, $oldDates)];
            $datas['allDay'][$a] = $oldAllDay[array_search($date, $oldDates)];
            $key = $newAmountYears[substr($date, 0, 4)] . ' ' . $date . ' ' . $datas['times'][$a]['initial'];
            $datas['events'][$key] = $oldEvents[$key];
        }
        return $datas;
    }

    /**
     * @param string $date
     * @param array $dayMonth
     * @return string
     */
    public function countCurrentWeek(string $date, array $dayMonth): string
    {
        $days = 0;
        for ($a = 0; $a < (substr($date, 5, 2) - 1); $a++) {
            $days += $dayMonth[$a];
        }
        $week = $days / 7;
        if (strpos($week, '.') !== false) {
            $week = substr(floor($week), 0, strpos($week, '.'));
        }
        return (date('w', strtotime(substr($date, 0, 8) . '01')) == 0) ? $week : $week + 1;
    }

    public function kunique(array $dates): array
    {
        $newDates = [];
        foreach ($dates as $key => $value) {
            $newDates[$value][] = $key;
        }
        return $newDates;
    }

    public function incrementValues(array $event, array $datas): array
    {
        if (count($datas) > 0) {
            foreach ($event as $keys => $values) {
                if (in_array($keys, ['datetime', 'repeat']) !== false) {
                    $subKeys = array_keys($values);
                    for ($a = 0; $a < count($subKeys); $a++) {
                        foreach ($values[$subKeys[$a]] as $key => $value) {
                            if ($keys == 'datetime') {
                                if ($subKeys[$a] == 'input') {
                                    $subSubKeys = array_keys($value);
                                    for ($b = 0; $b < count($subSubKeys); $b++) {
                                        if (in_array($subSubKeys[$b], ['initial', 'last']) !== false) {
                                            if ($key == 'times') {
                                                $event[$keys][$subKeys[$a]][$key][$subSubKeys[$b]]['value'] = $datas['allDay'] ? '00:00' : $datas[$key][$subSubKeys[$b]];
                                                if ($datas['allDay']) {
                                                    $event[$keys][$subKeys[$a]][$key][$subSubKeys[$b]]['readonly'] = true;
                                                }
                                            } else {
                                                if ($datas['allDay'] && $subSubKeys[$b] == 'last') {
                                                    $datas[$key][$subSubKeys[$b]] = date(
                                                        'Y-m-d',
                                                        strtotime(
                                                            $datas[$key][$subSubKeys[$b]] . ' +1 day'
                                                        )
                                                    );
                                                }
                                                $event[$keys][$subKeys[$a]][$key][$subSubKeys[$b]]['value'] = $datas[$key][$subSubKeys[$b]];
                                            }
                                        }
                                    }
                                }
                            } else {
                                if ($subKeys[$a] == 'input') {
                                    $event[$keys][$subKeys[$a]][$key]['value'] = isset($datas[$keys][$key]) ? $datas[$keys][$key] : '';
                                } elseif ($subKeys[$a] == 'select') {
                                    $event[$keys][$subKeys[$a]][$key]['default'] = isset($datas[$keys][$key]) ? $datas[$keys][$key] : '';
                                }
                            }
                        }
                    }
                } elseif ($keys == 'title') {
                    list($eventText, $user) = explode(' - ', $datas[$keys]);
                    $event[$keys]['span']['input']['value'] = $eventText;
                    $event[$keys]['span']['select']['default'] = $user;
                } elseif ($keys == 'allDay') {
                    $event[$keys]['input']['checked'] = $datas[$keys];
                } elseif (in_array($keys, ['address', 'details']) !== false) {
                    if ($keys == 'address') {
                        $event[$keys]['input']['value'] = $datas[$keys];
                    } else {
                        $event[$keys]['textarea']['text'] = $datas[$keys];
                    }
                }
            }
        }
        return $event;
    }
}