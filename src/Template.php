<?php
/** @noinspection PhpUndefinedVariableInspection */

namespace Calendars;

use Exception;
use stdClass;

/**
 * Class Template
 * @package Calendars
 * @author zezinho2511
 */
class Template
{

    /**
     * @var
     */
    protected $helper;

    /**
     * @var stdClass
     */
    protected $style;
    /**
     * @var array|string
     */
    protected $displayMode;
    /**
     * @var array
     */
    private $template = [
        'div' => '<div%s>%s</div>',
        'main' => '<main%s>%s</main>',
        'span' => '<span%s>%s</span>',
        'ul' => '<ul%s>%s</ul>',
        'li' => '<li%s>%s</li>',
        'img' => '<img src="%s"%s>',
        'link' => '<a href="%s"%s>%s</a>',
        'form' => '<form action="%s" method="%s"%s>%s</form>',
        'input' => '<input type="%s" name="%s" value="%s"%s>',
        'select' => '<select name="%s"%s>%s</select>',
        'option' => '<option value="%s"%s>%s</option>',
        'textarea' => '<textarea name="%s" rows="%s" cols="%s"%s>%s</textarea>'
    ];

    /**
     * Template constructor.
     * @param array $data
     * @param string $style
     * @throws Exception
     */
    public function __construct(array $data, string $style)
    {
        $this->displayMode = ($style == 'view') ? 'lists' : $style;
        $this->style = new stdClass();
        $style = ($style == 'view') ? [
            $style,
            'calendar',
            'user',
            'extra',
            'legend'
        ] : ($style == 'callback' ? ['calendar'] : [$style]);
        $this->style($style);
        foreach ($data as $key => $value) {
            if (in_array($key, ['times', 'users', 'dates', 'events']) !== false) {
                if (empty($data[$key])) {
                    throw new Exception('This ' . $key . ' key cannot be empty.');
                }
            }
            $this->$key = $value;
        }
        return $this;
    }

    /**
     * @param array $data
     * @return Template
     */
    private function style(array $data): Template
    {
        foreach ($data as $name) {
            $key = stripos($name, '_') !== false ? substr($name, stripos($name, '_')) : $name;
            $this->style->$key = (new Style())->$name();
        }
        return $this;
    }

    /**
     * @param Helper $helper
     * @param string $style
     * @param array $values
     * @return $this
     */
    public function setHelper(Helper $helper, string $style, array $values = []): Template
    {
        $this->helper = $helper;
        if (count($values) > 0) {
            if ($style == 'event') {
                $this->style->event = $this->helper->incrementValues($this->style->event, $values);
            }
        }
        return $this;
    }

    public function select(string $name, array $value, array $options = []): string
    {
        if (!isset($options['option'])) {
            $options['option'] = [];
        }
        foreach ($value as $newValue) {
            if (isset($options['option']['selected'])) {
                unset($options['option']['selected']);
            }
            if (isset($options['default'])) {
                if ($newValue['value'] == $options['default']) {
                    $options['option']['selected'] = true;
                    unset($options['option']['default']);
                }
            }
            $option[] = $this->formatTemplate(
                'option',
                [
                    $newValue['key'],
                    $this->formatAttributes($options['option']),
                    $newValue['value']
                ]
            );
        }
        unset($options['option']);
        return $this->formatTemplate(
            'select',
            [
                $name,
                $this->formatAttributes($options),
                implode('', $option)
            ]
        );
    }

    /**
     * @param string $helper
     * @param array $options
     * @return string
     */
    private function formatTemplate(string $helper, array $options): string
    {
        if (in_array($helper, ['div', 'span']) !== false) {
            if (!empty($options[0])) {
                $options[0] = " " . $options[0];
            }
        } elseif ($helper == 'input') {
            if (!empty($options[3])) {
                $options[3] = " " . $options[3];
            }
        }
        return vsprintf($this->template[$helper], $options);
    }

    /**
     * @param array $options
     * @param string|null $name
     * @return string
     */
    private function formatAttributes(array $options = [], string $name = null): string
    {
        $attr = '';
        if (!empty($options)) {
            foreach ($options as $key => $value) {
                $attr .= ' ' . trim($key) . '="' . trim($value) . '"';
            }
        }
        return $attr;
    }

    public function disabledHoliday(string $time, string $name, array $style): string
    {
        $active = $this->helper->createTheExtraInTheView('holiday');
        if ($active) {
            $style['div']['class'] .= ' disabled';
        }
        $style['div']['class'] .= ' holiday-js';
        return $this->div(
            $this->span(
                $time,
                $style['time']
            ) . $this->span(
                $name,
                $style['event']
            ),
            $style['div']
        );
    }

    /**
     * @param string $content
     * @param array $options
     * @return string
     */
    protected function div(string $content, array $options = []): string
    {
        return $this->formatTemplate(
            'div',
            [
                $this->formatAttributes($options),
                $content
            ]
        );
    }

    /**
     * @param string $content
     * @param array $options
     * @return string
     */
    protected function span(string $content, array $options = []): string
    {
        foreach (array_keys($options) as $key) {
            if (in_array($key, ['input', 'select']) !== false) {
                unset($options[$key]);
            }
        }
        if (in_array('link', array_keys($options)) !== false) {
            $newOptions = $options['link'];
            unset($options['link']);
            $content = ['name' => trim($content), 'url' => $newOptions['content']];
            unset($newOptions['content']);
            $content = isset($newOptions['options']) ? $this->link($content, $newOptions['options']) : $this->link(
                $content
            );
        }
        return $this->formatTemplate(
            'span',
            [
                $this->formatAttributes($options),
                $content
            ]
        );
    }

    /**
     * @param array $content
     * @param array $options
     * @return string
     */
    protected function link(array $content, array $options = []): string
    {
        return $this->formatTemplate(
            'link',
            [
                $content['url'],
                $this->formatAttributes($options),
                $content['name']
            ]
        );
    }

    public function disabledMoonPhases(string $name, array $style): string
    {
        $active = $this->helper->createTheExtraInTheView('holiday');
        if ($active) {
            $style['div']['class'] .= ' disabled';
        }
        $style['div']['class'] .= ' moonPhases-js';
        return $this->div(
            $this->span(
                'Fase da  lua: ' . $name,
                $style['event']
            ),
            $style['div']
        );
    }

    /**
     * @param array $datas
     * @param array $options
     * @return string
     */
    public function createEventConcealer(array $options = []): string
    {
        $actives = $this->helper->createTheExtraInTheView();
        $holiday = '';
        if (!$actives['holiday']) {
            $holiday = $this->div(
                $this->checkbox(
                    'feriados',
                    [
                        'label' => 'Feriados',
                        'value' => 'holiday-js',
                        'id' => 'holidaysDisabled',
                        'class' => 'checkbox'
                    ]
                ),
                ['class' => 'hiddenOrVisible']
            );
        }
        $moonphases = '';
        if ($actives['moonPhases']) {
            $moonphases = $this->div(
                $this->checkbox(
                    'moonPhase',
                    [
                        'label' => 'Fases da Lua',
                        'value' => 'moonPhases-js',
                        'id' => 'moonPhasesDisabled',
                        'class' => 'checkbox'
                    ]
                ),
                ['class' => 'hiddenOrVisible']
            );
        }
        return $this->div(
                $this->span(
                    '+',
                    $options['newEvent']['span']['icon']
                ) . $this->span(
                    'Novo evento',
                    $options['newEvent']['span']['text']
                ),
                $options['newEvent']['div']
            ) . $this->div(
                $this->div(
                    $this->span(
                        'Ocular ou mostrar eventos'
                    ),
                    ['class' => 'titleHiddenOrVisible']
                ) . $holiday . $moonphases,
                ['class' => 'checkboxes']
            );
    }

    public function checkbox(string $name, array $options = []): string
    {
        $label = $options['label'];
        unset($options['label']);
        $value = $options['value'];
        unset($options['value']);
        if (isset($options['checked'])) {
            if ($options['checked'] == false) {
                unset($options['checked']);
            }
        }
        $input = $this->formatTemplate(
            'input',
            [
                'checkbox',
                $name,
                $value,
                $this->formatAttributes($options)
            ]
        );
        return $input . $label;
    }

    /**
     * @param array $datas
     * @param array $options
     * @return string
     */
    protected function displayMode(array $datas, array $options = []): string
    {
        if ($this->helper->createTheDisplayModeInTheView()) {
            $keysPT = ['lista', 'dia', 'semana', 'mês', 'ano', 'anos'];
            $keys = ['lists', 'day', 'week', 'month', 'year', 'years'];
            $div = $options['div'];
            $li = $options['li'];
            $liClass = isset($li['class']) ? $li['class'] : '';
            $address = $li['link'];
            unset($li['link']);
            foreach ($keys as $key => $value) {
                $li['class'] = $value == $this->displayMode ? (empty($liClass) ? 'menu-active' : $liClass . ' menu-active') : $liClass . '';
                $link = $this->link(
                    [
                        'name' => $keysPT[$key],
                        'url' => (isset($datas[1]) ? $value . DS . $datas[0] . DS . $datas[1] : $value . DS . $datas[0])
                    ],
                    $address
                );
                $mode[] = $this->formatTemplate(
                    'li',
                    [
                        $this->formatAttributes($li),
                        $link
                    ]
                );
            }
            return $this->div(
                $this->formatTemplate(
                    'ul',
                    [
                        $this->formatAttributes([]),
                        implode('', $mode)
                    ]
                ),
                $div
            );
        }
        return '';
    }

    /**
     * @param string $content
     * @param array $options
     * @return string
     */
    protected function main(string $content, array $options = []): string
    {
        return $this->formatTemplate(
            'main',
            [
                $this->formatAttributes($options),
                $content
            ]
        );
    }

    /**
     * @param string $content
     * @param array $options
     * @return string
     */
    protected function img(string $content, array $options = []): string
    {
        return $this->formatTemplate(
            'img',
            [
                $content,
                $this->formatAttributes($options),
            ]
        );
    }

    /**
     * @param string $content
     * @param array $options
     * @return string
     */
    protected function form(string $content, array $options = []): string
    {
        if (isset($options['method'])) {
            $method = $options['method'];
            unset($options['method']);
        } else {
            $method = 'post';
        }
        if (!isset($options['autocomplete'])) {
            $options['autocomplete'] = 'off';
        }
        $action = isset($options['url']) ? $options['url'] : '';
        if (isset($options['url'])) {
            unset($options['url']);
        }
        if (!empty($this->view->request->base)) {
            $action = $this->view->request->base . $action;
        }
        return $this->formatTemplate(
            'form',
            [
                $action,
                $method,
                $this->formatAttributes($options),
                $content
            ]
        );
    }

    /**
     * @param string $type
     * @param string $name
     * @param array $options
     * @return string
     */
    protected function input(string $type, string $name, array $options = []): string
    {
        if (in_array(substr($name, 0, 4), ['date', 'time']) !== false) {
            $options = $options[strtolower(substr($name, 4))];
            if (substr($name, 0, 4) == 'date') {
                $options['value'] = implode('/', array_reverse(explode('-', $options['value'])));
            }
            unset($options[strtolower(substr($name, 4))]);
        }
        $value = '';
        if (isset($options['value'])) {
            $value = $options['value'];
            unset($options['value']);
        }
        return $this->formatTemplate(
            'input',
            [
                $type,
                $name,
                $value,
                $this->formatAttributes($options)
            ]
        );
    }

    /**
     * @param string $name
     * @param array $options
     * @return string
     */
    protected function textarea(string $name, array $options = []): string
    {
        $row = $options['row'];
        $col = $options['col'];
        $value = $options['text'];
        unset($options['text'], $options['row'], $options['col']);
        return $this->formatTemplate(
            'textarea',
            [
                $name,
                $row,
                $col,
                $this->formatAttributes($options),
                $value
            ]
        );
    }

    /**
     * @param string $data
     * @param string $title
     * @return string
     */
    protected function htmlTemplate(string $data, string $title): string
    {
        $title = substr($title, strripos($title, DS_REVERSE) + 1);
        $title = substr($title, 0, stripos($title, 'Template'));
        $css = ['icons.css', 'calendar.css'];
        $js = ['jquery.js', 'calendar.js', 'event.js'];
        $favicon = ASSETS . 'favicon' . DS . 'calendar.ico';
        $html = "<!DOCTYPE html>" . PHP_EOL;
        $html .= "<html>" . PHP_EOL;
        $html .= "<head>" . PHP_EOL;
        $html .= "<meta charset='UTF-8'>" . PHP_EOL;
        $html .= "<meta name='viewport' content ='width=device-width, initial-scale=1, shrink-to-fit=no'>" . PHP_EOL;
        $html .= "<title>Calendar: {$title}</title>" . PHP_EOL;
        $html .= "<link href='" . ASSETS . 'css' . DS . $css[0] . "' rel='stylesheet'>" . PHP_EOL;
        $html .= "<link href='" . ASSETS . 'css' . DS . $css[1] . "' rel='stylesheet'>" . PHP_EOL;
        $html .= "<link rel='shortcut icon' type='image/x-icon' href='{$favicon}'>" . PHP_EOL;
        $html .= "</head>" . PHP_EOL;
        $html .= "<body>" . PHP_EOL;
        $html .= $data . PHP_EOL;
        $html .= "<script src='" . ASSETS . 'js' . DS . $js[0] . "'></script>" . PHP_EOL;
        $html .= "<script src='" . ASSETS . 'js' . DS . $js[1] . "'></script>" . PHP_EOL;
        $html .= "<script src='" . ASSETS . 'js' . DS . $js[2] . "'></script>" . PHP_EOL;
        $html .= "</body>" . PHP_EOL;
        $html .= "</html>";
        return $html;
    }


}