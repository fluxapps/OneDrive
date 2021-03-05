<?php
use srag\DIC\OneDrive\Version\Version;
/**
 * Class exodUtil
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class exodUtil
{
    public static function getSystemMessageHTML(string $a_txt, string $a_type = "info") : string
    {
        global $DIC;
        $version = new Version();
        if ($version->is6()) {
            return ilUtil::getSystemMessageHTML($a_txt, $a_type);
        } else {
            return $DIC->ui()->mainTemplate()->getMessageHTML($a_txt, $a_type);
        }
    }
}
