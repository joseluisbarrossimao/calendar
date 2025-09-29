<?php

namespace Calendars;

use Calendars\callback\CallbackTemplate;
use Calendars\Day\DayTemplate;
use Calendars\Event\EventTemplate;
use Calendars\Lists\ListTemplate;
use Calendars\Month\MonthTemplate;
use Calendars\Week\WeekTemplate;
use Calendars\Year\YearTemplate;
use Exception;
use JsonException;

/**
 * Class Calendar
 * @package Calendars
 * @author zezinho2511
 */
class Calendar
{
    use DefaultTrait;

    /**
     * @var array
     */
    public $amountYear = [];
    /**
     * @var string
     */
    private $businessHours = '';
    /**
     * @var array
     */
    private $week = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
    /**
     * @var array
     */
    private $months = [
        'Janeiro',
        'Fevereiro',
        'Março',
        'Abril',
        'Maio',
        'Junho',
        'Julho',
        'Agosto',
        'Setembro',
        'Outubro',
        'Novembro',
        'Dezembro'
    ];
    /**
     * @var string
     */
    private $date;
    /**
     * @var array
     */
    private $holidays = [];
    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var string
     */
    private $displayMode;

    /**
     * @var string
     */
    private $user;

    /**
     * @var bool
     */
    private $formatHtml = false;

    /**
     * Cria a classe Calendar com os eventes requeridos na variavel $event.
     * Calendar constructor.
     * @param array $events variavel que vem como um array com a chave data e hora do evento e dentro um outro array
     *                      com evento e o userio do evento.
     * @param string $date variavel que seleciona os eventos daquele mes
     * @param array $datasActive deletar eventos dos dias de feriados
     * @param bool $displayMode ativar se vai criar o modo de exibição
     * @throws Exception erro de formato de data que não foi pre requerido (ex:2001-10-12)
     */
    public function __construct(array $events, string $date, array $datasActive = [], bool $displayMode = null)
    {
        $this->helper = new Helper(
            (isset($datasActive['holiday']) ? $datasActive['holiday'] : false),
            (!is_null($displayMode) ? $displayMode : true),
            (isset($datasActive['moonPhases']) ? $datasActive['moonPhases'] : true)
        );
        $newYear = substr($date, 0, 4) < (date('Y') - 30) ? substr($date, 0, 4) : date('Y');
        $this->helper->year($newYear);
        $this->date = $date;
        for ($a = -30; $a <= 30; $a++) {
            if ($a == 30) {
                $b = 1;
            }
            $year = $newYear + $a;
            $this->daysOfTheMonthInTheYear($year);
            $this->mobileHolidays($year, $a);
            $this->amountYear[$year] = $a;
        }
        $this->events($events, $this->amountYear);
        return $this;
    }

    /**
     * @param string $method variavel de metodo que vai ser executado para mostrar os eventos daquele mes
     * @param string $user variavel que vem com um usuário pre selecionado
     * @param string $businessHours definir o horario que vai aparecer no calendario
     * @return $this
     */
    public function extraDefine(string $method, string $user, string $businessHours = '00:00 até 23:59'): Calendar
    {
        $this->businessHours = $businessHours;
        $this->user = $user;
        $this->displayMode = $method;
        return $this;
    }

    /**
     * @param bool $active
     * @return $this
     */
    public function htmlInsert(bool $active): Calendar
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $active = !$active;
        }
        if ($active) {
            $this->formatHtml = true;
        }
        return $this;
    }


    /**
     * Adicionar feriados que são estaduais ou até municipais através da variável $date e a variável $holiday.
     * @param string $date variavel referenta a Data do feriado.
     * @param string $holiday variavel referente ao nome do feriado.
     * @return Calendar
     * @noinspection PhpUndefinedVariableInspection
     */
    public function extraHoliday(string $date, string $holiday): Calendar
    {
        $a = substr($this->dates[$a], 0, 4) - date("Y");
        $year = date("Y", strtotime($a . " year"));
        if ($year == substr($date, 0, 4)) {
            $this->holidays[$a . " " . $this->helper->countCurrentDays(
                $date,
                $this->daysOfTheMonthInTheYear
            ) . ' 00:00'] = implode(
                    '-',
                    array_reverse(
                        explode(
                            "/",
                            $date
                        )
                    )
                ) . " - " . $holiday;
        }
        return $this;
    }

    /**
     * @param string $callback
     * @param string $dataset
     * @param string $date
     * @param bool $cssSelectActive
     * @return false|string
     * @throws JsonException
     */
    public function displayMode(string $callback, string $dataset, string $date = '', bool $cssSelectActive = false)
    {
        if (empty($dataset)) {
            $dataset = 'month';
        }
        if (!empty($callback)) {
            if ($callback == 'assembly callback') {
                if (!empty($date)) {
                    $arrayDate = explode('-', $date);
                    if (in_array(count($arrayDate), [1, 2]) !== false) {
                        $date = $arrayDate[0] . substr($this->date, 4);
                    }
                } else {
                    $date = $this->date;
                }
                if (!$cssSelectActive) {
                    $cssSelectActive = $dataset == 'day' ? true : false;
                }
                $data = $this->callback(
                    $dataset,
                    ['select' => $date, 'today' => date('Y-m-d')],
                    $cssSelectActive
                );
                $height = $dataset == 'month' ? '36%' : '23%';
            } else {
                $data = $this->{$this->displayMode}();
            }
            return json_encode(
                isset($height) ? [
                    'data' => $data,
                    'height' => $height
                ] : [
                    'data' => $data
                ],
                JSON_THROW_ON_ERROR | JSON_FORCE_OBJECT
            );
        }
        return json_encode(
            [
                'schedule' => $this->{$this->displayMode}(),
                'calendar' => $this->callback($dataset, ['select' => $this->date, 'today' => date('Y-m-d')], true)
            ],
            JSON_THROW_ON_ERROR | JSON_FORCE_OBJECT
        );
    }

    private function callback(string $identifier, array $dates, bool $cssSelectActive): string
    {
        if ($identifier == 'months') {
            $identifier = 'year';
        }
        if ($identifier == 'year' || $identifier == 'years') {
            $keys = array_keys($this->amountYear);
            for ($a = 0; $a < count($keys); $a = $a + 10) {
                if (in_array(
                        substr($keys[$a], 2),
                        ['00', '10', '20', '30', '40', '50', '60', '70', '80', '90']
                    ) === false) {
                    $year = substr($keys[$a], 0, 3) . '0';
                } else {
                    $year = $keys[$a];
                }
                $datas['years'][] = $year . ' - ' . substr($year, 0, 3) . '9';
            }
        }
        $datas['cssSelectActive'] = $cssSelectActive;
        $datas['date'] = $dates;
        $datas['week'] = $this->week;
        $datas['weeks'] = $this->helper->countCurrentWeek(
            $dates['select'],
            $this->daysOfTheMonthInTheYear[substr($dates['select'], 0, 4)]
        );
        $datas['month'] = $this->months[substr($dates['select'], 5, 2) - 1];
        $datas['months'] = $this->months;
        $datas['year'] = substr($dates['select'], 0, 4);
        $datas['daysOfTheMonthInTheYear'] = $this->daysOfTheMonthInTheYear[substr($dates['select'], 0, 4)];
        $method = substr(__METHOD__, stripos(__METHOD__, "::") + 2);
        return (new CallbackTemplate($datas, $method))->setHelper($this->helper, $method)->render(
            $identifier
        );
    }

    /**
     * @return string
     * @throws Exception
     */
    public function view(): string
    {
        $datas['datasUsers'] = $this->datasUsers;
        $datas['users'] = $this->users;
        $datas['user'] = ['name' => $this->user];
        $datas['date'] = $this->date;
        $method = substr(__METHOD__, stripos(__METHOD__, "::") + 2);
        return (new ViewTemplate($datas, $method))->setHelper($this->helper, $method)->render(
            $this->formatHtml
        );
    }

    /**
     * mostrar o resultado de um evento completo para o seu framework
     * @param string $date variavel que traz a data do evento escolhido
     * @param string $time variavel que traz o horário do evento escolhido
     * @param string $event variavel que traz o titulo do evento escolhido
     * @param array $datas variavel em array que contem o id do evento, diferença entre datas
     *                     e diferença entre horário Ex:  [id=>3, datediff=>0, timediff=>15]
     * @param array $details vartiavel que traz os detalhes do evento escolhido
     * @return string retorna o evento montado e só imprimir
     * @throws Exception
     */
    public function event(string $date, string $time, string $event, array $datas, array $details): string
    {
        if (!empty($date)) {
            $key = [
                $this->amountYear[substr($date, 0, 4)],
                $this->helper->countCurrentDays($date, $this->daysOfTheMonthInTheYear[substr($date, 0, 4)]),
                $time
            ];
            $data = $this->events[implode(' ', $key)];
            foreach ($data as $key => $value) {
                list($title, $color) = explode(' - ', $value);
                if ($title == $event) {
                    break;
                }
            }
            foreach ($this->datasUsers as $key => $value) {
                if ($value['color'] == $color) {
                    $details['title'] = ucfirst($title) . ' - ' . $key;
                    break;
                }
            }
            $details['dates']['initial'] = $date;
            $details['times']['initial'] = $time;
            $details['dates']['last'] = $this->extra[$date][$color]['data-dateDiff'] !== $datas['dateDiff'] ? date(
                'Y-m-d',
                strtotime(
                    $date . ' +' . $datas['dateDiff'] . ' days'
                )
            ) : $date;
            $details['times']['last'] = date(
                'H:i',
                strtotime($time . ' +' . $datas['timeDiff'] . ' minutes')
            );
            if (!$details['allDay']) {
                if ($this->validAllDay[$details['dates']['initial']][$color] != $details['allDay']) {
                    $details['allDay'] = $this->validAllDay[$details['dates']['initial']][$color];
                }
            }
        } else {
            $details['title'] = ' - ';
            $details['dates'] = ['initial' => '', 'last' => ''];
            $details['times'] = ['initial' => '', 'last' => ''];
            if(!isset($details['allDay'])){
                $details['allDay']=false;
            }
        }
        $newDatas['week'] = $this->week;
        $newDatas['users'] = $this->users;
        $method = substr(__METHOD__, stripos(__METHOD__, "::") + 2);
        return (new EventTemplate($newDatas, $method))->setHelper($this->helper, $method, $details)->render();
    }

    /**
     * Mostra o resultado em uma lista por data de evento e também pode vir com um deteriminado usuário selecionado
     * no seu framework
     * @return string retorna a listagem de evento montado e só imprimir
     * @throws Exception erro de formato de data que não foi pre requerido (ex:2001-10-12)
     * @noinspection PhpUndefinedVariableInspection
     */
    private function lists(): string
    {
        if (!preg_match("/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/i", $this->date)) {
            throw new Exception('This date is not in the format required by the database (ex:2012-07-10)');
        }
        $holidays = [];
        $f = 0;
        foreach ($this->dates as $key => $value) {
            $dates[] = $value['initial'];
        }
        $keysDates = $this->helper->kunique($dates);
        for ($a = -30; $a <= 30; $a++) {
            if ($a >= $this->amountYear[substr($this->date, 0, 4)]) {
                $year = array_search($a, $this->amountYear);
                for ($b = 0; $b < 12; $b++) {
                    $days = $this->daysOfTheMonthInTheYear[$year][$b];
                    for ($c = 0; $c < $days; $c++) {
                        $newDate = $year . '-' . ($b < 9 ? '0' . ($b + 1) : ($b + 1)) . '-' . ($c < 9 ? '0' . ($c + 1) : ($c + 1));
                        $key = $a . ' ' . $this->helper->countCurrentDays(
                                $newDate,
                                $this->daysOfTheMonthInTheYear[substr($newDate, 0, 4)]
                            ) . ' 00:00';
                        if (isset($this->holidays[$key])) {
                            list($Date, $name) = explode(" - ", $this->holidays[$key]);
                            $datas['dates'][$f] = $Date;
                            $event[$f][] = $name;
                            $holidays[$f] = ['date' => $Date, 'name' => $name];
                            $daysHolidays[] = $Date;
                        }
                        $exists = 0;
                        $newEvent = [];
                        if (in_array($newDate, array_keys($keysDates)) !== false) {
                            $keys = $keysDates[$newDate];
                            for ($d = 0; $d < count($keys); $d++) {
                                if (!isset($datas['dates'][$f])) {
                                    $datas['dates'][$f] = $dates[$keys[$d]];
                                }
                                $newkey = $a . ' ' . $this->helper->countCurrentDays(
                                        $newDate,
                                        $this->daysOfTheMonthInTheYear[substr(
                                            $newDate,
                                            0,
                                            4
                                        )]
                                    ) . ' ' . $this->times[$keys[$d]]['initial'];
                                $newKeys = array_keys($this->events[$newkey]);
                                for ($e = 0; $e < count($newKeys); $e++) {
                                    if (in_array($this->events[$newkey][$newKeys[$e]], $newEvent) === false) {
                                        $time[$f][] = $this->times[$keys[$d]]['initial'] . ' até ' . $this->times[$keys[$d]]['last'];
                                        $exists++;
                                    }
                                }
                                if ($exists != 0) {
                                    $newEvent = $this->events[$newkey];
                                    $event[$f] = isset($event[$f]) ? array_merge($event[$f], $newEvent) : $newEvent;
                                    $exists = 0;
                                }
                            }
                        }
                        $f++;
                    }
                }
            }
        }
        unset($newDate);
        list($time, $event, $datas['dates']) = $this->helper->appearEventThatHasAHoliday(
            array_merge($datas, ['event' => $event, 'time' => $time]),
            $holidays,
            ['date' => $this->date, 'type' => substr(__METHOD__, strpos(__METHOD__, '::') + 2)]
        );
        $datas['moreThanOneEventAtTheSameTime'] = $time;
        foreach ($time as $key => $value) {
            for ($a = 0; $a < count($value); $a++) {
                $newTimeEnvent[$key][] = substr($value[$a], 0, stripos($value[$a], ' até '));
            }
        }
        for ($a = 0; $a < count($datas['dates']); $a++) {
            $key = [$this->amountYear[substr($datas['dates'][$a], 0, 4)], $datas['dates'][$a]];
            $datas['moonPhases'][] = $this->moonPhases($datas['dates'][$a]);
            for ($b = 0; $b <= 96; $b++) {
                if ($b > 0) {
                    $newTime = date("H:i", strtotime("00:00 +" . (($b - 1) * 15) . ' minutes'));
                    if (substr($this->businessHours, 0, 5) <= $newTime && substr(
                            $this->businessHours,
                            strripos($this->businessHours, ' ') + 1
                        ) >= $newTime) {
                        if (isset($newTimeEnvent[$a]) && in_array($newTime, $newTimeEnvent[$a]) !== false) {
                            $newEvent = [];
                            $keys = array_keys($event[$a]);
                            for ($c = 0; $c < count($keys); $c++) {
                                if ($newTimeEnvent[$a][$c] == $newTime) {
                                    $newEvent[] = $event[$a][$keys[$c]];
                                    unset($event[$a][$keys[$c]]);
                                }
                            }
                            $datas['events'][$a]['normal'][implode(' ', $key) . ' ' . $newTime][] = $newEvent;
                            $datas['times'][$a][] = $time[$a][array_search($newTime, $newTimeEnvent[$a])];
                            for ($c = 0; $c < count($keys); $c++) {
                                if ($newTimeEnvent[$a][$c] == $newTime) {
                                    unset($newTimeEnvent[$a][$c], $time[$a][$c]);
                                }
                            }
                            $event[$a] = $this->helper->ksort($event[$a]);
                            $time[$a] = $this->helper->ksort($time[$a]);
                            $newTimeEnvent[$a] = $this->helper->ksort($newTimeEnvent[$a]);
                        }
                    }
                } else {
                    if ($a == 13) {
                        $f = 1;
                    }
                    $datas['events'][$a]['holiday'][implode(' ', $key) . ' allDay'] = in_array(
                        $datas['dates'][$a],
                        $daysHolidays,
                        true
                    ) !== false ? $event[$a][0] : '';
                    $datas['times'][$a][$b] = 'allDay';
                    if (!empty($datas['events'][$a]['holiday'][implode(' ', $key) . ' allDay'])) {
                        unset($event[$a][0]);
                        $event[$a] = $this->helper->ksort($event[$a]);
                    }
                }
            }
        }
        $datas['extra'] = $this->extra;
        $datas['allDay'] = $this->validAllDay;
        $datas['datasUsers'] = $this->datasUsers;
        $datas['users'] = $this->users;
        $datas['user'] = ['name' => $this->user];
        $datas['amountYear'] = $this->amountYear;
        $datas['week'] = $this->week;
        $datas['daysOfTheMonthInTheYear'] = $this->daysOfTheMonthInTheYear[substr($this->date, 0, 4)];
        $method = substr(__METHOD__, stripos(__METHOD__, "::") + 2);
        return (new listTemplate($datas, $method))->setHelper($this->helper, $method)->render($this->formatHtml);
    }

    /**
     * mostrar o resultado de um dia no calendadio e também pode vir com um deteriminado usuário selecionado
     * no seu framework
     * @return string retorna o dia selecionado do montado e só imprimir
     * @throws Exception erro de formato de data que não foi pre requerido (ex:2001-10-12)
     */
    private function day(): string
    {
        if (!preg_match("/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/i", $this->date)) {
            throw new Exception('This date is not in the format required by the database (ex:2012-07-10)');
        }
        $amountYear = substr($this->date, 0, 4) - date("Y");
        if (substr($this->date, 0, 4) == date("Y")) {
            $amountYear = 0;
        }
        $key = $amountYear . ' ' . $this->helper->countCurrentDays(
                $this->date,
                $this->daysOfTheMonthInTheYear[substr($this->date, 0, 4)]
            ) . ' 00:00';
        $daysHolidays = $holidays = [];
        if (isset($this->holidays[$key])) {
            list($Date, $name) = explode(" - ", $this->holidays[$key]);
            $datas['dates'][] = $Date;
            $event[] = $name;
            $holidays[] = ['date' => $Date, 'name' => $name];
            $daysHolidays[] = $Date;
        }
        $exists = 0;
        $newEvent = [];
        foreach ($this->dates as $key => $value) {
            if ($value['initial'] == $this->date) {
                if (!isset($datas['dates'])) {
                    $datas['dates'][] = $value['initial'];
                }
                $time[] = $this->times[$key]['initial'];
                $newkey = $amountYear . ' ' . $this->helper->countCurrentDays(
                        $this->date,
                        $this->daysOfTheMonthInTheYear[substr(
                            $this->date,
                            0,
                            4
                        )]
                    ) . " " . $this->times[$key]['initial'];
                for ($a = 0; $a < count($this->events[$newkey]); $a++) {
                    if (in_array($this->events[$newkey][$a], $newEvent) === false) {
                        $exists++;
                    }
                }
                if ($exists != 0) {
                    $event = $newEvent = $this->events[$newkey];
                    $exists = 0;
                }
            }
        }
        if (!isset($datas['dates'])) {
            $datas['dates'][] = $this->date;
        }
        if (!isset($time)) {
            $time[] = '';
        }
        if (!isset($event)) {
            $event[] = '';
        }
        list($event, $time, $datas) = $this->helper->appearEventThatHasAHoliday(
            array_merge($datas, ['event' => $event, 'time' => $time]),
            $holidays,
            ['date' => $this->date, 'type' => substr(__METHOD__, strpos(__METHOD__, '::') + 2)]
        );
        for ($a = 0; $a <= 96; $a++) {
            if ($a > 0) {
                $newTime = date("H:i", strtotime("00:00 +" . (($a - 1) * 15) . ' minutes'));
                if (substr($this->businessHours, 0, 5) <= $newTime && substr(
                        $this->businessHours,
                        strripos($this->businessHours, ' ') + 1
                    ) >= $newTime) {
                    if (isset($time[$a]) && in_array($newTime, $time[$a]) !== false) {
                        $newEvent = [];
                        for ($b = 0; $b < count($events); $b++) {
                            if ($time[$b] == $newTime) {
                                $newEvent[] = $event[$b];
                                unset($event[$b]);
                            }
                        }
                        $event = $this->helper->ksort($event);
                        $datas['events'][$amountYear . ' ' . $datas['dates'][0] . ' ' . $newTime][] = $newEvent;
                        $datas['times'][] = $time[array_search($newTime, $time)];
                    } else {
                        $datas['events'][$amountYear . ' ' . $datas['dates'][0] . ' ' . $newTime][] = [];
                        $datas['times'][] = $newTime;
                    }
                }
            } else {
                $datas['events'][$a][$amountYear . ' ' . $datas['dates'][$a] . ' allDay'] = in_array(
                    $datas['dates'][$a],
                    $daysHolidays,
                    true
                ) !== false ? $event[$a][0] : '';
                $datas['times'][$a] = 'allDay';
                if (count($holidays) > 0) {
                    unset($event[0]);
                    $event = $this->helper->ksort($event);
                }
            }
        }
        $datas['users'] = $this->users;
        $datas['user'] = ['name' => $this->user];
        return (new DayTemplate($datas, substr(__METHOD__, stripos(__METHOD__, "::") + 2)))->setHelper(
            $this->helper
        )->render();
    }

    /**
     * mostrar o resultado de uma semana no calendadio e também pode vir com um deteriminado usuário selecionado
     * no seu framework
     * @return string retorna a semana selecinada do calendario montado e só imprimir
     * @throws Exception erro de formato de data que não foi pre requerido (ex:2001-10-12)
     */
    private function week(): string
    {
        if (!preg_match("/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/i", $this->date)) {
            throw new Exception('This date is not in the format required by the database (ex:2012-07-10)');
        }
        $weekInitial = (date("w", strtotime($this->date)) == 0) ? "0" : "-" . date("w", strtotime($this->date));
        $weekLast = (date("w", strtotime($this->date)) == 0) ? "1" : 6 - date("w", strtotime($this->date));
        $b = 0;
        $daysHolidays = $holidays = [];
        for ($a = $weekInitial; $a <= $weekLast; $a++) {
            $newDates[] = $newDate = date("Y-m-d", strtotime($this->date . $a . " days"));
            $amountYear = substr($newDate, 0, 4) - date("Y");
            if (substr($newDate, 0, 4) == date("Y")) {
                $amountYear = 0;
            }
            $key = $amountYear . ' ' . $this->helper->countCurrentDays(
                    $newDate,
                    $this->daysOfTheMonthInTheYear[substr($newDate, 0, 4)]
                ) . ' 00:00';
            if (isset($this->holidays[$key])) {
                list($Date, $name) = explode(" - ", $this->holidays[$key]);
                $datas['dates'][] = $Date;
                $event[$b][] = $name;
                $holidays[] = ['date' => $Date, 'name' => $name];
                $daysHolidays[] = $Date;
            }
            $exists = 0;
            $newEvent = [];
            foreach ($this->dates as $key => $value) {
                if (in_array($value['initial'], $newDates) !== false && $value['initial'] == $newDate) {
                    $datas['dates'][$b] = $value['initial'];
                    $time[$b][] = $this->times[$key]['initial'];
                    $newkey = $amountYear . ' ' . $this->helper->countCurrentDays(
                            $newDate,
                            $this->daysOfTheMonthInTheYear[substr(
                                $newDate,
                                0,
                                4
                            )]
                        ) . " " . $this->times[$key]['initial'];
                    for ($a = 0; $a < count($this->events[$newkey]); $a++) {
                        if (in_array($this->events[$newkey][$a], $newEvent) === false) {
                            $exists++;
                        }
                    }
                    if ($exists != 0) {
                        $event[$b] = $newEvent = $this->events[$newkey];
                        $exists = 0;
                    }
                }
            }
            if (!isset($datas['dates'][$b])) {
                $datas['dates'][$b] = $newDate;
            }
            if (!isset($time[$b])) {
                $time[$b][] = '';
            }
            if (!isset($event[$b])) {
                $event[$b][] = '';
            }
            $b++;
        }
        list($event, $time, $datas) = $this->helper->appearEventThatHasAHoliday(
            array_merge($datas, ['event' => $event, 'time' => $time]),
            $holidays,
            ['date' => $this->date, 'type' => substr(__METHOD__, strpos(__METHOD__, '::') + 2)]
        );
        $newEvent = [];
        for ($a = 0; $a < count($datas['dates']); $a++) {
            for ($b = 0; $b <= 96; $b++) {
                if ($b > 0) {
                    $newTime = date("H:i", strtotime("00:00 +" . (($b - 1) * 15) . ' minutes'));
                    if (substr($this->businessHours, 0, 5) <= $newTime && substr(
                            $this->businessHours,
                            strripos($this->businessHours, ' ') + 1
                        ) >= $newTime) {
                        if (in_array($newTime, $time) !== false) {
                            for ($c = 0; $c < count($event[$a]); $c++) {
                                if ($time[$a][$c] == $newTime) {
                                    $newEvent[] = $event[$a][$c];
                                    unset($event[$a][$c]);
                                }
                            }
                            $event[$a] = $this->helper->ksort($event[$a]);
                            $datas['events'][$a][$amountYear . ' ' . $datas['dates'][$a] . ' ' . $newTime][] = $newEvent;
                            $datas['times'][$a][] = $time[$a][array_search($newTime, $time[$a])];
                        } else {
                            $datas['events'][$a][$amountYear . ' ' . $datas['dates'][$a] . ' ' . $newTime][] = [];
                            $datas['times'][$a][] = $newTime;
                        }
                    }
                } else {
                    $datas['events'][$a][$amountYear . ' ' . $datas['dates'][$a] . ' allDay'] = in_array(
                        $datas['dates'][$a],
                        $daysHolidays,
                        true
                    ) !== false ? $event[$a][0] : '';
                    $datas['times'][$a][$b] = 'allDay';
                    if (count($holidays) > 0) {
                        unset($event[$a][0]);
                        $event[$a] = $this->helper->ksort($event[$a]);
                    }
                }
            }
        }
        $datas['users'] = $this->users;
        $datas['user'] = ['name' => $this->user];
        return (new WeekTemplate($datas, substr(__METHOD__, stripos(__METHOD__, "::") + 2)))->setHelper(
            $this->helper
        )->render();
    }

    /**
     * mostrar o resultado de um mes no calendadio e também pode vir com um deteriminado usuário selecionado
     * no seu framework
     * @return string retorna o mes selecionado do calendario montado e só imprimir
     * @throws Exception erro de formato de data que não foi pre requerido (ex:2001-10-12)
     */
    private function month(): string
    {
        if (!preg_match("/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/i", $this->date)) {
            throw new Exception('This date is not in the format required by the database (ex:2012-07-10)');
        }
        $days = $this->daysOfTheMonthInTheYear[substr($this->date, 0, 4)][substr($this->date, 5, 2) - 1];
        $b = 0;
        $daysHolidays = $holidays = [];
        for ($a = 1; $a <= $days; $a++) {
            $newDates[] = $newDate = substr($this->date, 0, 8) . (($a < 10) ? '0' . $a : $a);
            $amountYear = substr($newDate, 0, 4) - date("Y");
            if (substr($newDate, 0, 4) == date("Y")) {
                $amountYear = 0;
            }
            $key = $amountYear . ' ' . $this->helper->countCurrentDays(
                    $newDate,
                    $this->daysOfTheMonthInTheYear[substr($newDate, 0, 4)]
                ) . ' 00:00';
            if (isset($this->holidays[$key])) {
                list($Date, $name) = explode(" - ", $this->holidays[$key]);
                $datas['dates'][$b] = $Date;
                $event[$b][] = $name;
                $holidays[] = ['date' => $Date, 'name' => $name];
                $daysHolidays[] = $Date;
            }
            $exists = 0;
            $newEvent = [];
            foreach ($this->dates as $key => $value) {
                if (in_array($value['initial'], $newDates) !== false && $value['initial'] == $newDate) {
                    if (!isset($datas['dates'][$b])) {
                        $datas['dates'][$b] = $value['initial'];
                    }
                    $time[$b][] = $this->times[$key]['initial'];
                    $newkey = $amountYear . ' ' . $this->helper->countCurrentDays(
                            $newDate,
                            $this->daysOfTheMonthInTheYear[substr(
                                $newDate,
                                0,
                                4
                            )]
                        ) . " " . $this->times[$key]['initial'];
                    for ($a = 0; $a < count($this->events[$newkey]); $a++) {
                        if (in_array($this->events[$newkey][$a], $newEvent) === false) {
                            $exists++;
                        }
                    }
                    if ($exists != 0) {
                        $event[$b] = $newEvent = $this->events[$newkey];
                        $exists = 0;
                    }
                }
            }
            if (!isset($datas['dates'][$b])) {
                $datas['dates'][$b] = $newDate;
            }
            if (!isset($time[$b])) {
                $time[$b][] = '';
            }
            if (!isset($event[$b])) {
                $event[$b][] = '';
            }
            $b++;
        }
        list($event, $time, $datas) = $this->helper->appearEventThatHasAHoliday(
            array_merge($datas, ['event' => $event, 'time' => $time]),
            $holidays,
            ['date' => $this->date, 'type' => substr(__METHOD__, strpos(__METHOD__, '::') + 2)]
        );
        for ($a = 0; $a < count($datas['dates']); $a++) {
            for ($b = 0; $b <= 96; $b++) {
                $newEvent = [];
                if ($b > 0) {
                    $newTime = date("H:i", strtotime("00:00 +" . (($b - 1) * 15) . ' minutes'));
                    if (substr($this->businessHours, 0, 5) <= $newTime && substr(
                            $this->businessHours,
                            strripos($this->businessHours, ' ') + 1
                        ) >= $newTime) {
                        if (in_array($newTime, $time[$a]) !== false) {
                            for ($c = 0; $c < count($event[$a]); $c++) {
                                if ($time[$a][$c] == $newTime) {
                                    $newEvent[] = $event[$a][$c];
                                    unset($event[$a][$c]);
                                }
                            }
                            $event[$a] = $this->helper->ksort($event[$a]);
                            $datas['events'][$a][$amountYear . ' ' . $datas['dates'][$a] . ' ' . $newTime][] = $newEvent;
                            $datas['times'][$a][] = $time[$a][array_search($newTime, $time[$a])];
                        }
                    }
                } else {
                    $datas['events'][$a][$amountYear . ' ' . $datas['dates'][$a] . ' allDay'] = in_array(
                        $datas['dates'][$a],
                        $daysHolidays,
                        true
                    ) !== false ? $event[$a][0] : '';
                    $datas['times'][$a][$b] = 'allDay';
                    if (in_array(($a + 1), $daysHolidays) !== false) {
                        $event[$a][0] = '';
                        if (count($event[$a]) > 1) {
                            unset($event[$a][0]);
                        }
                        $event[$a] = $this->helper->ksort($event[$a]);
                    }
                }
            }
        }
        $datas['users'] = $this->users;
        $datas['user'] = ['name' => $this->user];
        return (new MonthTemplate($datas, substr(__METHOD__, stripos(__METHOD__, "::") + 2)))->setHelper(
            $this->helper
        )->render();
    }

    /**
     * mostrar o resultado de um ano no calendadio e também pode vir com um deteriminado usuário selecionado
     * no seu framework
     * @return string retorna o ano selecionado do calendario montado e só imprimir
     * @throws Exception erro de formato de data que não foi pre requerido (ex:2001-10-12)
     */
    private function year(): string
    {
        if (!preg_match("/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/i", $this->date)) {
            throw new Exception('This date is not in the format required by the database (ex:2012-07-10)');
        }
        $year = substr($this->date, 0, 5);
        $daysOfTheMonth = $this->daysOfTheMonthInTheYear[substr($this->date, 0, 4)];
        $daysHolidays = $holidays = [];
        $c = 0;
        for ($a = 0; $a < count($daysOfTheMonth); $a++) {
            $days = $daysOfTheMonth[$a];
            for ($b = 1; $b <= $days; $b++) {
                $newDates[] = $newDate = $year . (($a < 9) ? '0' . ($a + 1) : ($a + 1)) . '-' . (($b < 10) ? '0' . $b : $b);
                $amountYear = substr($newDate, 0, 4) - date("Y");
                if (substr($newDate, 0, 4) == date("Y")) {
                    $amountYear = 0;
                }
                $key = $amountYear . ' ' . $this->helper->countCurrentDays(
                        $newDate,
                        $daysOfTheMonth
                    ) . ' 00:00';
                if (isset($this->holidays[$key])) {
                    list($Date, $name) = explode(" - ", $this->holidays[$key]);
                    $datas['dates'][$c] = $Date;
                    $event[$c][] = $name;
                    $holidays[] = ['date' => $Date, 'name' => $name];
                    $daysHolidays[] = $Date;
                }
                foreach ($this->dates as $key => $value) {
                    if (in_array($value['initial'], $newDates) !== false && $value['initial'] == $newDate) {
                        if (!isset($datas['dates'][$c])) {
                            $datas['dates'][$c] = $value['initial'];
                        }
                        $time[$c][] = $this->times[$key]['initial'];
                        $newkey = $amountYear . ' ' . $this->helper->countCurrentDays(
                                $newDate,
                                $this->daysOfTheMonthInTheYear[substr(
                                    $newDate,
                                    0,
                                    4
                                )]
                            ) . " " . $this->times[$key]['initial'];
                        $event[$c] = isset($event[$c]) ? array_merge(
                            $event[$c],
                            $this->events[$newkey]
                        ) : $this->events[$newkey];
                    }
                }
                if (!isset($datas['dates'][$c])) {
                    $datas['dates'][$c] = $newDate;
                }
                if (!isset($time[$c])) {
                    $time[$c][] = '';
                }
                if (!isset($event[$c])) {
                    $event[$c][] = '';
                }
                $c++;
            }
        }
        list($event, $time, $datas) = $this->helper->appearEventThatHasAHoliday(
            array_merge($datas, ['event' => $event, 'time' => $time]),
            $holidays,
            ['date' => $this->date, 'type' => substr(__METHOD__, strpos(__METHOD__, '::') + 2)]
        );
        for ($a = 0; $a < count($datas['dates']); $a++) {
            for ($b = 0; $b <= 96; $b++) {
                $newEvent = [];
                if ($b > 0) {
                    $newTime = date("H:i", strtotime("00:00 +" . (($b - 1) * 15) . ' minutes'));
                    if (substr($this->businessHours, 0, 5) <= $newTime && substr(
                            $this->businessHours,
                            strripos($this->businessHours, ' ') + 1
                        ) >= $newTime) {
                        if (in_array($newTime, $time[$a]) !== false) {
                            for ($c = 0; $c < count($event[$a]); $c++) {
                                if ($time[$a][$c] == $newTime) {
                                    $newEvent[] = $event[$a][$c];
                                    unset($event[$a][$c]);
                                }
                            }
                            $event[$a] = $this->helper->ksort($event[$a]);
                            $datas['events'][$a][$amountYear . ' ' . $datas['dates'][$a] . ' ' . $newTime][] = $newEvent;
                            $datas['times'][$a][] = $time[$a][array_search($newTime, $time[$a])];
                        }
                    }
                } else {
                    $datas['events'][$a][$amountYear . ' ' . $datas['dates'][$a] . ' allDay'] = in_array(
                        $datas['dates'][$a],
                        $daysHolidays,
                        true
                    ) !== false ? $event[$a][0] : '';
                    $datas['times'][$a][$b] = 'allDay';
                    if (in_array(($a + 1), $daysHolidays) !== false) {
                        $event[$a][0] = '';
                        if (count($event[$a]) > 1) {
                            unset($event[$a][0]);
                        }
                        $event[$a] = $this->helper->ksort($event[$a]);
                    }
                }
            }
        }
        $datas['users'] = $this->users;
        $datas['user'] = ['name' => $this->user];
        return (new YearTemplate($datas, substr(__METHOD__, stripos(__METHOD__, "::") + 2)))->setHelper(
            $this->helper
        )->render();
    }

}