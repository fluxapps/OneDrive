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
     * @param ilTemplate $ilTemplate
     */
    public static final function init(string $type, ilTemplate $ilTemplate)/*: void*/
    {
        if (self::$init === false) {
            self::$init = true;

            $dir = __DIR__;
            $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

            $ilTemplate->addCss($dir . "/css/waiter.css");

            $ilTemplate->addJavaScript($dir . "/js/waiter.min.js");
        }

        $ilTemplate->addOnLoadCode('il.waiter.init("' . $type . '");');
    }
}
