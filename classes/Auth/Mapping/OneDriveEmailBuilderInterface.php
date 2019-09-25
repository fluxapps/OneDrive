<?php

/**
 * Interface EmailMappingInterface
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
Interface OneDriveEmailBuilderInterface
{

    /**
     * @return static
     */
    public static function getInstance();


    /**
     * @param ilObjUser $user
     *
     * @return string
     */
    public function getOneDriveEmailForUser(ilObjUser $user);
}