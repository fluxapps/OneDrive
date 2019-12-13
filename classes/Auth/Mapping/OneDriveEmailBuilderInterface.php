<?php

/**
 * Interface EmailMappingInterface
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
Interface OneDriveEmailBuilderInterface
{

    /**
     * @param ilObjUser $user
     *
     * @return string
     */
    public function getOneDriveEmailForUser(ilObjUser $user);
}