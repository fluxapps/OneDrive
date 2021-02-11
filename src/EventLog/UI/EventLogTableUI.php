<?php
namespace srag\Plugins\OneDrive\EventLog\UI;

use srag\Plugins\OneDrive\EventLog\EventLogEntryAR;
use ilTemplate;
use ILIAS\DI\Container;

/**
 * Class EventLogTableUI
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class EventLogTableUI
{
    /**
     * @var Container
     */
    protected $dic;

    /**
     * EventLogTableUI constructor.
     * @param Container $dic
     */
    public function __construct(Container $dic)
    {
        $this->dic = $dic;
    }

    public function render() : string
    {
        $tpl = new ilTemplate(__DIR__ . '/table/build/index.html', false, false);
        $data = array_map(function($entry) {
            return array_map(function($value) {
               return $value[1];
            }, $entry->getArrayForConnector());
        }, EventLogEntryAR::get());
        return '<script type="application/javascript">' .
            'window.exod_log_data = ' . json_encode(array_values($data)) . ';' .
            'window.lng = "' . $this->dic->language()->getLangKey() . '";' .
            '</script>'
            . $tpl->get();
    }
}
