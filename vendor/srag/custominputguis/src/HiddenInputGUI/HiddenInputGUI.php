<?php

namespace srag\CustomInputGUIs\OneDrive\HiddenInputGUI;

use ilHiddenInputGUI;
use srag\CustomInputGUIs\OneDrive\Template\Template;
use srag\DIC\OneDrive\DICTrait;

/**
 * Class HiddenInputGUI
 *
 * @package srag\CustomInputGUIs\OneDrive\HiddenInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class HiddenInputGUI extends ilHiddenInputGUI
{

    use DICTrait;

    /**
     * HiddenInputGUI constructor
     *
     * @param string $a_postvar
     */
    public function __construct(string $a_postvar = "")
    {
        parent::__construct($a_postvar);
    }


    /**
     * @return string
     */
    public function render() : string
    {
        $tpl = new Template("Services/Form/templates/default/tpl.property_form.html", true, true);

        $this->insert($tpl);

        return self::output()->getHTML($tpl);
    }
}
