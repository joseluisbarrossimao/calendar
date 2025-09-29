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
            'period' => 'each',
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
$details = [
    'details' => '',
    'address' => ''
];
if (!empty($_POST['subject'])) {
    $details = [
        'details' => $_POST['subject'] == 'audiência' ? 'Audiência contra Miguel Couto' : '',
        'address' => $_POST['subject'] == 'audiência' ? 'R. do Lavradio, 132' : ($_POST['subject'] != 'Férias' ? 'Av. Dom Hélder Câmara, 5200' : ''),
        'allDay' => false
    ];
}
$calendar = new Calendar(
    $eventos,
    (isset($_POST['date']) ? (!empty($_POST['date']) ? $_POST['date'] : date('Y-m-d')) : '2019-10-04')
);
echo $calendar->event(
    $_POST['date'],
    $_POST['time'],
    $_POST['subject'],
    ['id' => $_POST['id'], 'dateDiff' => $_POST['dateDiff'], 'timeDiff' => $_POST['timeDiff']],
    $details
);

//Já está ok