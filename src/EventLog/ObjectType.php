<?php
namespace srag\Plugins\OneDrive\EventLog;

use ilCloudException;

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
    public static function fromValue(string $type)
    {
        if (!in_array($type, self::$types)) {
            throw new ilCloudException('OneDrive EventLog: unknown object type "' . $type . '"');
        }
        return new self($type);
    }

    public function value() : string
    {
        return $this->type;
    }

}
