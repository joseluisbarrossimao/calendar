<?php


namespace Calendars\Year;

use Calendars\Template;

class YearTemplate extends Template
{

    public function render(): string
    {
        return $this->htmlTemplate('', __CLASS__);
    }

}