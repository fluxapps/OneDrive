<?php

namespace srag\Plugins\OneDrive\Waiter;

use ilTemplate;

/**
 * Class Waiter
 * @package srag\CustomInputGUIs\OpenCast\Waiter
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Waiter
{

    /**
     * @var string
     */
    const TYPE_PERCENTAGE = "percentage";
    /**
     * @var string
     */
    const TYPE_WAITER = "waiter";
    /**
     * @var bool
     */
    protected static $init = false;


    /**
     * Waiter constructor
     */
    private function __construct(){}

    /**
     * @param string     $type
     */
    public static final function init(string $type)/*: void*/
    {
        global $DIC;
        if (self::$init === false) {
            self::$init = true;

            $dir = __DIR__;
            $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

            $DIC->ui()->mainTemplate()->addCss($dir . "/css/waiter.css");

            $DIC->ui()->mainTemplate()->addJavaScript($dir . "/js/waiter.min.js");
        }

        $DIC->ui()->mainTemplate()->addOnLoadCode('il.waiter.init("' . $type . '");');
    }
}
