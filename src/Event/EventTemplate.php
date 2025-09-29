<?php

namespace Calendars\Event;

use Calendars\Template;

class EventTemplate extends Template
{

    public function render(): string
    {
        $select[] = ['key' => '', 'value' => 'Selecionar usuário'];
        foreach (array_keys($this->users) as $users) {
            $select[] = ['key' => str_replace(' ', '_', $users), 'value' => $users];
        }
        $week['initial'] = $this->span('', $this->style->event['datetime']['span']['week']['initial']);
        if (!empty($this->style->event['datetime']['input']['dates']['initial']['value'])) {
            $week['initial'] = $this->span(
                $this->week[date(
                    "w",
                    strtotime(
                        $this->style->event['datetime']['input']['dates']['initial']['value']
                    )
                )],
                $this->style->event['datetime']['span']['week']['initial']
            );
        }
        $week['last'] = $this->span('', $this->style->event['datetime']['span']['week']['last']);
        if (!empty($this->style->event['datetime']['input']['dates']['last']['value'])) {
            $week['last'] = $this->span(
                $this->week[date(
                    "w",
                    strtotime(
                        $this->style->event['datetime']['input']['dates']['last']['value']
                    )
                )],
                $this->style->event['datetime']['span']['week']['last']
            );
        }
        return $this->div(
            $this->form(
                $this->div(
                    $this->span(
                        $this->input(
                            'text',
                            'event',
                            $this->style->event['title']['span']['input']
                        ) . ' - ' . $this->select(
                            'user',
                            $select,
                            $this->style->event['title']['span']['select']
                        ),
                        $this->style->event['title']['span']
                    ) . $this->span('X', $this->style->event['title']['close']),
                    $this->style->event['title']['div']
                ) . $this->div(
                    $this->div(
                        $this->input(
                            'text',
                            'dateInitial',
                            $this->style->event['datetime']['input']['dates']
                        ) . $week['initial'] . " as " . $this->input(
                            'text',
                            'timeInitial',
                            $this->style->event['datetime']['input']['times']
                        ) . " até " . $this->input(
                            'text',
                            'dateLast',
                            $this->style->event['datetime']['input']['dates']
                        ) . $week['last'] . " as " . $this->input(
                            'text',
                            'timeLast',
                            $this->style->event['datetime']['input']['times']
                        ),
                        $this->style->event['datetime']['div']
                    ) . $this->div(
                        $this->checkbox(
                            'allDay',
                            $this->style->event['allDay']['input']
                        ),
                        $this->style->event['allDay']['div']
                    ),
                    $this->style->event['dataTimeAllDay']['div']
                ) . $this->div(
                    $this->div(
                        'Repetir ' . $this->select(
                            'period',
                            [
                                ['key' => '', 'value' => 'Selecionar período de repetição'],
                                ['key' => 'each', 'value' => 'A cada'],
                                ['key' => 'every days', 'value' => 'Todos os dias']
                            ],
                            $this->style->event['repeat']['select']['period']
                        ) . $this->input(
                            'text',
                            'amount',
                            $this->style->event['repeat']['input']['amount']
                        ) . $this->select(
                            'method',
                            [
                                ['key' => '', 'value' => 'Selecionar o tipo de repetição'],
                                ['key' => 'days', 'value' => 'Dias'],
                                ['key' => 'weeks', 'value' => 'Semanas'],
                                ['key' => 'months', 'value' => 'Mêses'],
                                ['key' => 'years', 'value' => 'Anos'],
                            ],
                            $this->style->event['repeat']['select']['method']
                        ),
                        $this->style->event['repeat']['subdiv1']
                    ) . $this->div(
                        ' durante ' . $this->input(
                            'text',
                            'number',
                            $this->style->event['repeat']['input']['number']
                        ) . $this->select(
                            'flow',
                            [
                                ['key' => '', 'value' => 'Selecionar o fluxo de dias'],
                                ['key' => 'rum', 'value' => 'Dias corridos'],
                                ['key' => 'useful', 'value' => 'Dias úteis'],
                            ],
                            $this->style->event['repeat']['select']['flow']
                        ),
                        $this->style->event['repeat']['subdiv2']
                    ),
                    $this->style->event['repeat']['div']
                ) . $this->div(
                    $this->div(
                        'Localizção e detalhes do evento',
                        $this->style->event['text']['div']
                    ),
                    $this->style->event['tabs']['div']
                ) . $this->div(
                    'Endereço: ' . $this->input(
                        'text',
                        'endereço',
                        $this->style->event['address']['input']
                    ) . 'Detalhes: ' . $this->textarea(
                        'detalhes',
                        $this->style->event['details']['textarea']
                    )
                ) . $this->div(
                    $this->input(
                        'submit',
                        'Salvar',
                        $this->style->event['buttons']['input']['salvar']
                    ) . $this->input(
                        'reset',
                        'limpar',
                        $this->style->event['buttons']['input']['limpar']
                    ),
                    $this->style->event['buttons']['div']
                ),
                $this->style->event['form']
            ),
            $this->style->event['div']
        );
    }
}