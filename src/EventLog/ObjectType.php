<?php
namespace srag\Plugins\OneDrive\EventLog;

use ilCloudException;
use exodItem;
use exodFile;
use exodFolder;

/**
 * Class ObjectType
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ObjectType
{
    const TYPE_FILE = 'file';
    const TYPE_FOLDER = 'folder';

    protected static $types = [
        self::TYPE_FOLDER,
        self::TYPE_FILE
    ];

    /**
     * @var string
     */
    protected $type;

    /**
     * ObjectType constructor.
     * @param string $type
     */
    protected function __construct(string $type)
    {
        $this->type = $type;
    }

    public static function file() : self
    {
        return new self(self::TYPE_FILE);
    }

    public static function folder() : self
    {
        return new self(self::TYPE_FOLDER);
    }

    /**
     * @param string $type
     * @return ObjectType
     * @throws ilCloudException
     */
    public static function fromValue(string $type) : self
    {
        if (!in_array($type, self::$types)) {
            throw new ilCloudException('OneDrive EventLog: unknown object type "' . $type . '"');
        }
        return new self($type);
    }

    /**
     * @param exodItem $item
     * @return static
     * @throws ilCloudException
     */
    public static function fromExodItem(exodItem $item) : self
    {
        if ($item instanceof exodFile) {
            return self::file();
        }
        if ($item instanceof exodFolder) {
            return self::folder();
        }
        throw new ilCloudException('OneDrive EventLog: unknown item type "' . get_class($item) . '"');
    }

    public function value() : string
    {
        return $this->type;
    }

}
