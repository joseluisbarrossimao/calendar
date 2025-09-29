<?php


namespace Calendars;


class ViewTemplate extends Template
{
    public function render(bool $activeHtml): string
    {
        $datas[] = $this->date;
        $user = '';
        $styleUserDiv = $this->style->user['user']['div']['class'];
        foreach ($this->users as $key => $value) {
            if ($key == $this->user['name']) {
                $datas[] = str_replace(' ', '_', $key);
                $this->style->user['user']['div']['class'] = $styleUserDiv . ' user-active';
            } elseif (!empty($this->user['name'])) {
                $this->style->user['user']['div']['class'] = $styleUserDiv . ' user-not-active';
            }
            $newUser = $this->img(
                    $this->datasUsers[$key]['img'],
                    array_merge(
                        $this->style->user['img'],
                        ['style' => 'border:5px solid ' . $this->datasUsers[$key]['color'] . ';']
                    )
                ) . $this->div(
                    $this->span(
                        $key
                    ) . $this->span(
                        $this->datasUsers[$key]['description']
                    ),
                    ['class' => 'userNameDescription']
                );
            $user .= $this->div(
                $this->link(
                    [
                        'name' => $newUser,
                        'url' => $this->displayMode . DS . $datas[0] . DS . str_replace(' ', '_', $key)
                    ],
                    $this->style->user['link']
                ),
                $this->style->user['user']['div']
            );
        }
        $schedule =
            $this->div(
                $user,
                $this->style->user['div']
            ) . $this->div(
                $this->displayMode(
                    $datas,
                    $this->style->view['display']
                ) . $this->div(
                    '',
                    $this->style->view['schedule']['div']
                ),
                $this->style->view['div']
            ) . $this->div(
                '',
                $this->style->calendar['div']
            ) . $this->div(
                $this->div(
                    $this->span(
                        'Legendas'
                    ),
                    ['class' => 'titleLegend']
                ) . $this->div(
                    $this->span(
                        $this->link(['url' => '', 'name' => '00'], ['class' => 'disabledLink']),
                        $this->style->legend['today']['span']['display']
                    ) . $this->span(
                        'Dia selecionado',
                        $this->style->legend['today']['span']['text']
                    ),
                    $this->style->legend['today']['div']
                ) . $this->div(
                    $this->span(
                        $this->link(['url' => '', 'name' => '00'], ['class' => 'disabledLink']),
                        $this->style->legend['selected']['span']['display']
                    ) . $this->span(
                        'Dia de hoje',
                        $this->style->legend['selected']['span']['text']
                    ),
                    $this->style->legend['selected']['div']
                ) . $this->div(
                    $this->span(
                        $this->link(['url' => '', 'name' => '00'], ['class' => 'disabledLink']),
                        $this->style->legend['selectedAndToday']['span']['display']
                    ) . $this->span(
                        'Dia de hoje e selecionado',
                        $this->style->legend['selectedAndToday']['span']['text']
                    ),
                    $this->style->legend['selectedAndToday']['div']
                ),
                $this->style->legend['div']
            ) . $this->div(
                $this->createEventConcealer($this->style->extra),
                $this->style->extra['div']
            ) . $this->div(
                $this->div(
                    '',
                    [
                        'class' => 'modal',
                        'id' => 'event'
                    ]
                ),
                [
                    'class' => 'event-modal',
                    'id' => 'modal'
                ]
            );
        if ($activeHtml) {
            return $this->htmlTemplate($schedule, __CLASS__);
        }
        return $schedule;
    }
}