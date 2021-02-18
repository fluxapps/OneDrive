<?php
namespace srag\Plugins\OneDrive\EventLog\UI;

use srag\Plugins\OneDrive\EventLog\EventLogEntryAR;
use ilTemplate;
use ILIAS\DI\Container;
use ilObjUser;

/**
 * Class EventLogTableUI
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class EventLogTableUI
{
    /**
     * @var int
     */
    private $obj_id;
    /**
     * @var Container
     */
    protected $dic;

    /**
     * EventLogTableUI constructor.
     * @param Container $dic
     * @param int       $obj_id
     */
    public function __construct(Container $dic, int $obj_id)
    {
        $this->dic = $dic;
        $this->obj_id = $obj_id;
    }

    public function render() : string
    {
        $tpl = new ilTemplate(__DIR__ . '/table/build/index.html', false, false);
        $data = array_map(function($entry) {
            $arrayForConnector = $entry->getArrayForConnector();
            array_walk($arrayForConnector, function(&$value, $key) {
               $value = $key === 'user_id' ?
                   ilObjUser::_lookupFullname($value[1]) . ' [' . ilObjUser::_lookupLogin($value[1]) . ']'
                   : $value[1];
            });
            return $arrayForConnector;
        }, EventLogEntryAR::where(['obj_id' => $this->obj_id])->get());
        return '<script type="application/javascript">' .
            'window.exod_log_data = ' . json_encode(array_values($data)) . ';' .
            'window.lng = "' . $this->dic->language()->getLangKey() . '";' .
            '</script>'
            . $tpl->get();
    }
}
