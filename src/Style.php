<?php


namespace Calendars;


class Style
{

    public function event()
    {
        return [
            'return' => [
                'span' => [],
                'input' => []
            ],
            'title' => [
                'span' => [
                    'class' => 'title',
                    'input' => [
                        'value' => ''
                    ],
                    'select' => [
                        'default' => ''
                    ]
                ],
                'close' => [
                    'class' => 'event-close',
                    'id' => 'close'
                ],
                'div' => [
                    'class' => 'event-title'
                ]
            ],
            'dataTimeAllDay' => [
                'div' => [
                    'class' => 'event-dateTimeAllDay'
                ]
            ],
            'datetime' => [
                'span' => [
                    'week' => [
                        'initial' => [
                            'id' => 'weekDefineInitial'
                        ],
                        'last' => [
                            'id' => 'weekDefineLast'
                        ]
                    ]
                ],
                'input' => [
                    'dates' => [
                        'initial' => [
                            'value' => '',
                            'data-mask' => '00/00/0000',
                            'class' => 'date-input-js'
                        ],
                        'last' => [
                            'value' => '',
                            'data-mask' => '00/00/0000',
                            'class' => 'datev-js'
                        ]
                    ],
                    'times' => [
                        'initial' => [
                            'value' => '',
                            'data-mask' => '00:00',
                            'class' => 'time-input-js'
                        ],
                        'last' => [
                            'value' => '',
                            'data-mask' => '00:00',
                            'class' => 'time-input-js'
                        ],
                        'class' => 'event-times'
                    ]
                ],
                'div' => [
                    'class' => 'event-dateTime'
                ]
            ],
            'allDay' => [
                'input' => [
                    'label' => 'Dia todo',
                    'value' => '00:00 dia atual às 00:00 dia seguinte',
                    'checked' => false,
                    'class' => 'checkbox',
                    'id' => 'allDay'
                ],
                'div' => [
                    'class' => 'event-allDay'
                ]
            ],
            'repeat' => [
                'input' => [
                    'amount' => [
                        'class' => 'event-amount disabled',
                        'id' => 'amountDisabled',
                        'maxlength' => 3,
                        'max' => 999,
                        'value' => ''
                    ],
                    'number' => [
                        'class' => 'event-duration-number',
                        'maxlength' => 3,
                        'max' => 999,
                        'value' => ''
                    ],
                ],
                'select' => [
                    'method' => [
                        'option' => [],
                        'class' => 'event-method disabled',
                        'id' => 'methodDisabled',
                        'default' => ''
                    ],
                    'period' => [
                        'option' => [],
                        'class' => 'event-period',
                        'id' => 'choosePeriod',
                        'default' => ''
                    ],
                    'flow' => [
                        'option' => [],
                        'id' => 'flowEdit',
                        'default' => ''
                    ]
                ],
                'subdiv1' => [
                    'class' => 'event-methodAmountPeriod'
                ],
                'subdiv2' => [
                    'class' => 'event-duration disabled',
                    'id' => 'durationDisabled'
                ],
                'div' => [
                    'class' => 'event-div'
                ],
            ],
            'text' => [
                'div' => [
                    'class' => 'event-localizationDetails'
                ]
            ],
            'tabs' => [
                'div' => [
                    'class' => 'event-tabs'
                ]
            ],
            'address' => [
                'input' => [
                    'class' => 'event-address',
                    'value' => ''
                ]
            ],
            'details' => [
                'textarea' => [
                    'class' => 'event-details',
                    'row' => '3',
                    'col' => '10',
                    'text' => ''
                ]
            ],
            'buttons' => [
                'input' => [
                    'salvar' => [
                        'class' => 'event-buttonsSave',
                        'value' => 'Salvar'
                    ],
                    'limpar' => [
                        'class' => 'event-buttonsReset',
                        'value' => 'Limpar'
                    ]
                ],
                'div' => [
                    'class' => 'event-buttons'
                ]
            ],
            'form' => [
                'class' => 'event-form'
            ],
            'div' => [
                'class' => 'event'
            ],
        ];
    }

    public function month()
    {
        return [
            'div' => [
                'class' => 'mode'
            ],
            'subdiv' => [],
            'week' => [
                'div' => [],
                'span' => []
            ],
            'weeks' => [
                'div' => [],
                'span' => []
            ],
            'day' => [
                'div' => [],
                'span' => []
            ],
            'time' => [
                'div' => [],
                'span' => []
            ],
            'event' => [
                'div' => [
                    'class' => ''
                ],
                'span' => []
            ],
            'identify' => [
                'div' => [],
                'span' => []
            ],
            'holiday' => [
                'span' => []
            ],
            'display' => [
                'ul' => [],
                'li' => [
                    'link' => []
                ],
                'div' => []
            ],
        ];
    }

    public function year()
    {
        return [
            'div' => [
                'class' => 'mode'
            ],
            'subdiv' => [],
            'week' => [
                'div' => [],
                'span' => []
            ],
            'weeks' => [
                'div' => [],
                'span' => []
            ],
            'day' => [
                'div' => [],
                'span' => []
            ],
            'time' => [
                'div' => [],
                'span' => []
            ],
            'event' => [
                'div' => [
                    'class' => ''
                ],
                'span' => []
            ],
            'identify' => [
                'div' => [],
                'span' => []
            ],
            'holiday' => [
                'span' => []
            ],
            'display' => [
                'ul' => [],
                'li' => [
                    'link' => []
                ],
                'div' => []
            ],
        ];
    }


    public function day()
    {
        return [
            'div' => [
                'class' => 'mode'
            ],
            'subdiv' => [],
            'day' => [
                'div' => [],
                'span' => []
            ],
            'time' => [
                'div' => [],
                'span' => []
            ],
            'event' => [
                'div' => [],
                'span' => []
            ],
            'identify' => [
                'div' => [],
                'span' => []
            ],
            'holiday' => [
                'span' => []
            ],
            'display' => [
                'ul' => [],
                'li' => [
                    'link' => []
                ],
                'div' => []
            ],
        ];
    }

    public function week()
    {
        return [
            'div' => [
                'class' => 'mode'
            ],
            'subdiv' => [],
            'day' => [
                'div' => [],
                'span' => []
            ],
            'time' => [
                'div' => [],
                'span' => []
            ],
            'event' => [
                'div' => [],
                'span' => []
            ],
            'identify' => [
                'div' => [],
                'span' => []
            ],
            'holiday' => [
                'span' => []
            ],
            'display' => [
                'ul' => [],
                'li' => [
                    'link' => []
                ],
                'div' => []
            ],
        ];
    }

    public function legend()
    {
        return [
            'div' => [
                'class' => 'legend'
            ],
            'today' => [
                'div' => [
                    'class' => 'today'
                ],
                'span' => [
                    'display' => [
                        'class' => 'span-select legend-span'
                    ],
                    'text' => [
                        'class' => 'span-select-text'
                    ]
                ]
            ],
            'selected' => [
                'div' => [
                    'class' => 'selected'
                ],
                'span' => [
                    'display' => [
                        'class' => 'span-active legend-span'
                    ],
                    'text' => [
                        'class' => 'span-active-text'
                    ]
                ]
            ],
            'selectedAndToday' => [
                'div' => [
                    'class' => 'selectedAndToday legend-span'
                ],
                'span' => [
                    'display' => [
                        'class' => 'span-selectAndActive'
                    ],
                    'text' => [
                        'class' => 'span-selectedAndToday-text'
                    ]
                ]
            ]
        ];
    }

    public function calendar(): array
    {
        return [
            'div' => [
                'class' => 'calendar'
            ],
            'day' => [
                'div' => [
                    'class' => 'day-eight'
                ]
            ],
            'days' => [
                'div' => [
                    'class' => 'day'
                ],
                'span' => [
                    'class' => 'day-js',
                    'link' => [
                        'content' => '',
                        'options' => [
                        ]
                    ]
                ]
            ],
            'week' => [
                'div' => [
                    'class' => 'week'
                ]
            ],
            'month' => [
                'div' => [
                    'class' => 'month-four'
                ],
                'span' => [
                    'class' => 'month',
                    'link' => [
                        'content' => '',
                        'options' => []
                    ],
                ]
            ],
            'months' => [
                'div' => [
                    'class' => 'months'
                ],
                'span' => [
                    'link' => [
                        'content' => '',
                        'options' => []
                    ]
                ]
            ],
            'year' => [
                'div' => [
                    'class' => 'year-four'
                ],
                'span' => [
                    'class' => 'year',
                    'link' => [
                        'content' => '',
                        'options' => []
                    ]
                ]
            ],
            'years' => [
                'div' => [
                    'class' => 'years'
                ],
                'span' => [
                    'link' => [
                        'content' => '',
                        'options' => []
                    ]
                ]
            ],
            'identifier' => [
                'div' => [
                    'class' => 'identifier',
                ],
                'span' => [
                    'prev' => [
                        'class' => 'prev',
                        'link' => [
                            'content' => '#',
                            'options' => []
                        ]
                    ],
                    'identification' => [
                        'link' => [
                            'content' => '#',
                            'options' => []
                        ]
                    ],
                    'next' => [
                        'class' => 'next',
                        'link' => [
                            'content' => '#',
                            'options' => []
                        ]
                    ]
                ]
            ],
        ];
    }

    public function view(): array
    {
        return [
            'div' => [
                'class' => 'mode'
            ],
            'display' => [
                'li' => [
                    'link' => ['class' => 'menu-js']
                ],
                'div' => ['class' => 'menu']
            ],
            'schedule' => [
                'div' => [
                    'class' => 'schedule'
                ]
            ]
        ];
    }

    public function extra()
    {
        return [
            'div' => [
                'class' => 'extra'
            ],
            'newEvent' => [
                'div' => [
                    'class' => 'new-event',
                    'id' => 'eventReset'
                ],
                'span' => [
                    'icon' => [],
                    'text' => []
                ]
            ]
        ];
    }

    public function lists(): array
    {
        return [
            'div' => [
                'class' => 'list'
            ],
            'date' => [
                'div' => ['class' => 'date-js-father'],
                'span' => ['class' => 'date-js-children']
            ],
            'week' => [
                'div' => [],
                'span' => []
            ],
            'event' => [
                'div' => [
                    'class' => 'events event-js'
                ],
                'span' => [
                    'class' => 'events-span'
                ]
            ],
            'time' => [
                'div' => [],
                'span' => ['class' => 'time time-span time-js']
            ],
            'day' => [
                'div' => ['class' => 'father'],
            ],
            'year' => [
                'div' => [],
                'span' => []
            ],
            'holiday' => [
                'div' => ['class' => 'holiday'],
                'event' => ['class' => 'event-holiday'],
                'time' => [
                    'class' => 'time time-holiday'
                ]
            ],
            'moonPhases' => [
                'div' => [
                    'class' => 'moonPhase'
                ],
                'event' => []
            ],
            'display' => [
                'li' => [
                    'link' => []
                ],
                'div' => ['class' => 'menu']
            ],
        ];
    }

    public function user(): array
    {
        return [
            'div' => [
                'class' => 'users'
            ],
            'user' => [
                'div' => [
                    'class' => 'user'
                ],
                'span' => [
                    'class' => ''
                ]
            ],
            'link' => [
                'class' => 'user-link'
            ],
            'img' => [
                'class' => 'img'
            ]
        ];
    }

}