<?php

/**
 * Class ilOneDriveException
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ilOneDriveException extends ilCloudException
{
    const RENAME_FAILED = 2601;

    const ERROR_NAME_ALREADY_EXISTS = 'nameAlreadyExists';

    const MSG_ILLEGAL_CHARACTERS = 'The provided name cannot contain any illegal characters.';
    const MSG_LEADING_OR_TRAILING_SPACES = 'The provided name cannot contain leading, or trailing, spaces.';

    /**
     * @param stdClass $error
     *
     * @return ilOneDriveException
     */
    public static function fromErrorResponse(stdClass $error)
    {
        switch ($error->code) {
            case self::ERROR_NAME_ALREADY_EXISTS:
                return new self(self::RENAME_FAILED, ilOneDrivePlugin::getInstance()->txt('msg_name_already_exists'));
            default:
                switch ($error->message) {
                    case self::MSG_ILLEGAL_CHARACTERS:
                        return new self(self::RENAME_FAILED, ilOneDrivePlugin::getInstance()->txt('msg_illegal_characters'));
                    case self::MSG_LEADING_OR_TRAILING_SPACES:
                        return new self(self::RENAME_FAILED, ilOneDrivePlugin::getInstance()->txt('msg_leading_or_trailing_spaces'));
                    default:
                        return new self(self::UNKNONW_EXCEPTION, $error->message);
                }
        }
    }

    /**
     *
     */
    protected function assignMessageToCode()
    {
        switch ($this->code) {
            case self::RENAME_FAILED:
                $this->message = ilOneDrivePlugin::getInstance()->txt('msg_rename_failed');
                $this->message .= ($this->add_info ? ": " : "") . $this->add_info;
                break;
            default:
                parent::assignMessageToCode();
                break;
        }
    }
}