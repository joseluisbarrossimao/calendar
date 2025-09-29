<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Calendars\Calendar;

$imgUrl = $_SERVER['REQUEST_SCHEME'] . ':' . DS . DS . $_SERVER['SERVER_NAME'] . substr(
        $_SERVER['SCRIPT_NAME'],
        0,
        stripos(
            $_SERVER['SCRIPT_NAME'],
            'index'
        )
    );
$eventos = [
    [
        'extra' => ['idQuery' => 3],
        'dates' => ['initial' => '2019-10-04', 'last' => '2019-10-04'],
        'times' => ['initial' => '11:15', 'last' => '11:30'],
        'event' => 'audiência',
        'repeat' => [
            'amount' => '2',
            'period' => 'every',
            'method' => 'days',
            'duration' => [
                'number' => 30,
                'flow' => 'useful'
            ],
        ],
        'allDay' => false,
        'user' => [
            'name' => 'Suporte 12',
            'img' => $imgUrl . 'example' . DS . 'img' . DS . '1.jpg',
            'description' => 'Suporte 1',
            'color' => '#A9A9A9'
        ]
    ],
    [
        'extra' => ['idQuery' => 2],
        'dates' => ['initial' => '2019-10-04', 'last' => '2019-10-04'],
        'times' => ['initial' => '15:30', 'last' => '15:45'],
        'event' => 'cardiologista',
        'allDay' => false,
        'user' => [
            'name' => 'Suporte 11',
            'img' => $imgUrl . 'example' . DS . 'img' . DS . '2.jpg',
            'description' => 'Desenvolvedor',
            'color' => '#FF69B4'
        ]
    ],
    [
        'extra' => ['idQuery' => 3],
        'dates' => ['initial' => '2019-10-04', 'last' => '2019-10-04'],
        'times' => ['initial' => '16:00', 'last' => '16:15'],
        'event' => 'Férias',
        'allDay' => false,
        'user' => [
            'name' => 'Suporte 14',
            'img' => $imgUrl . 'example' . DS . 'img' . DS . '3.jpg',
            'description' => 'Suporte 2',
            'color' => '#DAA520'
        ]
    ]
];
$calendar = new Calendar(
    $eventos,
    (isset($_POST['date']) ? $_POST['date'] : '2019-10-04')
);
//não é preciso coloca a função htmlInsert, so se quiser o html todo montado
$calendar->htmlInsert(stripos(__DIR__, 'example') !== false);
//extradefine é obrodatorio pra uso do componente
$calendar->extraDefine(
    (isset($_POST['method']) ? $_POST['method'] : 'list'),
    (isset($_POST['user']) ? str_replace('_', ' ', $_POST['user']) : '')
);
if (isset($_POST['method'])) {
    echo $calendar->displayMode(
        $_POST['callback']['text'],
        (isset($_POST['callback']['method']) ? $_POST['callback']['method'] : ''),
        (isset($_POST['callback']['date']) ? $_POST['callback']['date'] : '')
    );
    exit();
}
echo $calendar->view();
