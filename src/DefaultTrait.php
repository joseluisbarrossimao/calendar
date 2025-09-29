<?php
/** @noinspection ALL */

namespace Calendars;

use DateTime;
use Exception;

/**
 * Trait DefaultTrait
 * @package Calendars
 */
trait DefaultTrait
{

    /**
     * @var array
     */
    public $daysOfTheMonthInTheYear = [];

    /**
     * @var array
     */
    public $events = [];

    /**
     * @var array
     */
    public $datasUsers = [];

    /**
     * @var array
     */
    public $validAllDay = [];

    /**
     * @var array
     */
    public $extra = [];

    /**
     * @var array
     */
    public $dates = [];

    /**
     * @var array
     */
    public $times = [];

    /**
     * @var array
     */
    public $users = [];

    /**
     * @param string $year
     * @return $this
     */
    public function daysOfTheMonthInTheYear(string $year): self
    {
        for ($a = 1; $a <= 12; $a++) {
            $this->daysOfTheMonthInTheYear[$year][] = cal_days_in_month(CAL_GREGORIAN, $a, $year);
        }
        return $this;
    }

    /**
     * @param string $date
     * @return string
     */
    public function moonPhases(string $date): string
    {
        list($year, $month, $day) = explode('-', $date);
        if ($month < '03') {
            $year--;
            $month += 12;
        }
        $month++;
        $jd = ((365.25 * $year) + (30.6 * $month) + $day - 694039.09) / 29.5305882;
        $b = intval($jd);
        $jd -= $b;
        $b = round($jd * 8);
        if ($b >= 8) {
            $b = 0;
        }
        switch ($b) {
            case 0:
                $phase = 'Lua nova';
                break;
            case 1:
                $phase = 'Lua crescente côncava';
                break;
            case 2:
                $phase = 'Lua quarto crescente';
                break;
            case 3:
                $phase = 'Lua crescente convexa';
                break;
            case 4:
                $phase = 'Lua cheia';
                break;
            case 5:
                $phase = 'Lua minguante convexa';
                break;
            case 6:
                $phase = 'Lua quarto minguante';
                break;
            case 7:
                $phase = 'Lua minguante côncava';
                break;
        }
        return $phase;
    }

    /**
     * Adiciona os feriados moveis que são: páscoa, carnaval, sexta feira santa, corpus christi, dia das maes e
     * dia dos pais.
     * @param string $year
     * @param int $countYear
     * @return $this
     */
    private function mobileHolidays(string $year, int $countYear): self
    {
        $this->fiexdHolidays($countYear);
        $Date = $this->easterDate($year);
        $this->holidays[$countYear . " " . $this->helper->countCurrentDays(
            $Date,
            $this->daysOfTheMonthInTheYear[$year]
        ) . ' 00:00'] = $Date . ' - Páscoa';
        $date = date('Y-m-d', strtotime($Date . '-47 days'));
        $this->holidays[$countYear . " " . $this->helper->countCurrentDays(
            $date,
            $this->daysOfTheMonthInTheYear[$year]
        ) . ' 00:00'] = $date . ' - Carnaval';
        $date = date('Y-m-d', strtotime($Date . '-2 days'));
        $this->holidays[$countYear . " " . $this->helper->countCurrentDays(
            $date,
            $this->daysOfTheMonthInTheYear[$year]
        ) . ' 00:00'] = $date . ' - Sexta Feira Santa';
        $date = date('Y-m-d', strtotime($Date . '+60 days'));
        $this->holidays[$countYear . " " . $this->helper->countCurrentDays(
            $date,
            $this->daysOfTheMonthInTheYear[$year]
        ) . ' 00:00'] = $date . ' - Corpus Christi';
        for ($a = 1; $a <= 15; $a++) {
            if (date('w', strtotime($year . '-05-' . $a)) == 0) {
                break;
            }
        }
        $date = $year . '-05-' . (($a + 7) < 10 ? "0" . ($a + 7) : ($a + 7));
        $this->holidays[$countYear . " " . $this->helper->countCurrentDays(
            $date,
            $this->daysOfTheMonthInTheYear[$year]
        ) . ' 00:00'] = $date . ' - Dia das Mães';
        for ($a = 1; $a <= 15; $a++) {
            if (date('w', strtotime($year . '-08-' . $a)) == 0) {
                break;
            }
        }
        $date = $year . '-08-' . (($a + 7) < 10 ? "0" . ($a + 7) : ($a + 7));
        $this->holidays[$countYear . " " . $this->helper->countCurrentDays(
            $date,
            $this->daysOfTheMonthInTheYear[$year]
        ) . ' 00:00'] = $date . ' - Dia dos Pais';
        return $this;
    }

    /**
     * Feriados fixados que nunca iram mudar a data
     * @return $this
     */
    private function fiexdHolidays(int $countYear): self
    {
        $holidays = [
            '01/01' => 'Confraternização Universal',
            '21/04' => 'Tiradentes',
            '01/05' => 'Dia do Trabalho',
            '07/09' => 'Independência do Brasil',
            '12/10' => 'Nossa Senhora Aparecida',
            '02/11' => 'Finados',
            '15/11' => 'Proclamação da República',
            '25/12' => 'Natal'
        ];
        foreach ($holidays as $key => $value) {
            $date = $key . "/" . date("Y", strtotime($countYear . " year"));
            $this->holidays[$countYear . " " . $this->helper->countCurrentDays(
                $date,
                $this->daysOfTheMonthInTheYear[substr($date, 6)]
            ) . ' 00:00'] = implode(
                    "-",
                    array_reverse(
                        explode(
                            "/",
                            $date
                        )
                    )
                ) . " - " . $value;
        }
        return $this;
    }

    /**
     * Resolve o dia que vai cair a pascoa naquele ano
     * @param string $year
     * @return string
     */
    public function easterDate(string $year): string
    {
        $result = ((19 * ($year % 19) + 24) % 30) + ((2 * ($year % 4) + 4 * ($year % 7) + 6 * ((19 * ($year % 19) + 24) % 30) + 5) % 7);
        if ($result > 9) {
            $day = $result - 9;
            $month = 4;
        } else {
            $day = $result + 22;
            $month = 3;
        }
        if (($day == 26) and ($month == 4)) {
            $day = 19;
        }
        if (($day == 25) and ($month == 4) and (((19 * ($year % 19) + 24) % 30) == 28) and (($year % 19) > 10)) {
            $day = 18;
        }
        if ($month < 10) {
            $month = "0" . $month;
        }
        if ($day < 10) {
            $day = "0" . $day;
        }
        return $year . "-" . $month . "-" . $day;
    }

    /**
     * cria o evento não inportando se vai repertir
     * @param array $events
     * @param array $amountYears
     * @return $this
     */
    private function events(array $events, array $amountYears): self
    {
        for ($a = 0; $a < count($events); $a++) {
            $this->datasUsers[$events[$a]['user']['name']] = [];
            if (isset($events[$a]['repeat'])) {
                $b = 0;
                $methodRepeat = $events[$a]['repeat']['method'];
                $date = $events[$a]['dates'];
                $jump = $execute = true;
                if (isset($events[$a]['repeat']['duration'])) {
                    $duration = date(
                        'Y-m-d',
                        strtotime(
                            $date['initial'] . ' +' . $events[$a]['repeat']['duration']['number'] . 'days'
                        )
                    );
                    if ($events[$a]['repeat']['duration']['flow'] == 'useful') {
                        $dayWeek = date('w', strtotime($duration));
                        if ($dayWeek == 6) {
                            $duration = date('Y-m-d', strtotime($duration . ' +2 days'));
                        } elseif ($dayWeek == 0) {
                            $duration = date('Y-m-d', strtotime($duration . ' +1 days'));
                        }
                    }
                }
                if (isset($events[$a]['repeat']['delete'])) {
                    $dateDelete = array_map("trim", explode(',', $events[$a]['repeat']['delete']));
                }
                $total = $methodRepeat == 'days' ? $this->helper->totalCurrentDays(
                    $events[$a]['dates']['initial'],
                    $this->daysOfTheMonthInTheYear
                ) : ($methodRepeat == 'month' ? 30 * 12 : 30);
                while ($b < $total) {
                    $dates['initial'] = date('Y-m-d', strtotime($date['initial'] . ' +' . $b . $methodRepeat));
                    $dates['last'] = date('Y-m-d', strtotime($date['last'] . ' +' . $b . $methodRepeat));
                    if (in_array(substr($dates['initial'], 0, 4), array_keys($this->amountYear)) === false) {
                        break;
                    }
                    $amountYear = $this->amountYear[substr($dates['initial'], 0, 4)];
                    if (isset($duration)) {
                        $execute = ($dates['initial'] <= $duration);
                    }
                    if ($execute) {
                        if (isset($dateDelete)) {
                            $jump = !(in_array($dates['initial'], $dateDelete) !== false);
                        }
                        if ($jump) {
                            $this->createEventAttr(
                                $amountYear,
                                $dates,
                                $events[$a]['times'],
                                $events[$a]['user'],
                                $events[$a]['event'],
                                $events[$a]['extra'],
                                (count($this->datasUsers[$events[$a]['user']['name']]) > 0 ? false : true),
                                $events[$a]['allDay']
                            );
                        }
                    }
                    if ($events[$a]['repeat']['period'] != 'each') {
                        $b++;
                    } else {
                        $b = $b + $events[$a]['repeat']['amount'];
                    }
                }
            } else {
                $this->createEventAttr(
                    $amountYears[substr($events[$a]['dates']['initial'], 0, 4)],
                    $events[$a]['dates'],
                    $events[$a]['times'],
                    $events[$a]['user'],
                    $events[$a]['event'],
                    $events[$a]['extra'],
                    (count($this->datasUsers[$events[$a]['user']['name']]) > 0 ? false : true),
                    $events[$a]['allDay']
                );
            }
        }
        return $this;
    }

    /**
     * guarda todos dados daquele evento referente do calendario
     * @param string $amountYear
     * @param array $date
     * @param array $time
     * @param array $user
     * @param string $event
     * @param array $extra
     * @param bool $exist
     * @param bool $activeAllDay
     * @return $this
     */
    public function createEventAttr(
        string $amountYear,
        array $date,
        array $time,
        array $user,
        string $event,
        array $extra,
        bool $exist,
        bool $activeAllDay
    ): self {
        $nameUser = $user['name'];
        unset($user['name']);
        $count = count($this->dates);
        $this->extradetail('idQuery', $extra['idQuery'], [$date, $user['color'], $time]);
        if (!isset($this->validAllDay[$date['initial']]) || array_key_exists(
                $user['color'],
                $this->validAllDay[$date['initial']]
            ) === false) {
            $this->validAllDay[$date['initial']][$user['color']] = $activeAllDay;
        }
        $this->dates[$count] = $date;
        $this->times[$count] = $time;
        $this->events[$amountYear . " " . $this->helper->countCurrentDays(
            $this->dates[$count]['initial'],
            $this->daysOfTheMonthInTheYear[substr(
                $this->dates[$count]['initial'],
                0,
                4
            )]
        ) . " " . $this->times[$count]['initial']][$nameUser] = $event . ' - ' . $user['color'];
        $this->users[$nameUser][] = $amountYear . " " . $this->helper->countCurrentDays(
                $this->dates[$count]['initial'],
                $this->daysOfTheMonthInTheYear[substr(
                    $this->dates[$count]['initial'],
                    0,
                    4
                )]
            ) . " " . $this->times[$count]['initial'];
        if ($exist) {
            $this->datasUsers[$nameUser] = $user;
        }
        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @param array $keyExtra
     * @return $this
     * @throws Exception
     */
    public function extraDetail(string $key, string $value, array $keyExtra): self
    {
        list($date, $color, $time) = $keyExtra;
        if (!isset($this->extra[$date['initial']]) || in_array($color, $this->extra[$date['initial']]) === false) {
            if (count($time) == 1) {
                $time = ['initial' => '00:00', 'last' => '00:00'];
            }
            $dateTime = (new DateTime($date['initial'] . ' ' . $time['initial']))->diff(
                new DateTime($date['last'] . ' ' . $time['last'])
            );
            $extra = [
                'data-' . $key => $value,
                'data-dateDiff' => $dateTime->format('%r%a'),
                'data-timeDiff' => $dateTime->format('%r%i')
            ];
            $this->extra[$date['initial']][$color] = $extra;
        }
        return $this;
    }
}