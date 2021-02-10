<?php
namespace srag\Plugins\OneDrive\EventLog;

/**
 * Class EventLogRepository
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class EventLogger
{
    public static function logUploadStarted(
        int $user_id,
        string $file_name,
        string $parent
    ) {
        self::log(
            $user_id,
            EventType::uploadStarted(),
            $file_name,
            ObjectType::file(),
            $parent,
            []
        );
    }
    
    public static function logUploadComplete(
        int $user_id,
        string $file_name,
        string $parent
    ) {
        self::log(
            $user_id,
            EventType::uploadComplete(),
            $file_name,
            ObjectType::file(),
            $parent,
            []
        );
    }
    
    public static function logUploadAborted(
        int $user_id,
        string $file_name,
        string $parent
    ) {
        self::log(
            $user_id,
            EventType::uploadAborted(),
            $file_name,
            ObjectType::file(),
            $parent,
            []
        );
    }
    
    public static function logObjectDeleted(
        int $user_id,
        string $object_name,
        ObjectType $object_type,
        string $parent
    ) {
        self::log(
            $user_id,
            EventType::uploadAborted(),
            $object_name,
            $object_type,
            $parent,
            []
        );
    }
    
    public static function logObjectRenamed(
        int $user_id,
        string $object_name_old,
        string $object_name_new,
        ObjectType $object_type,
        string $parent
    ) {
        self::log(
            $user_id,
            EventType::uploadAborted(),
            $object_name_old,
            $object_type,
            $parent,
            ['new_name' => $object_name_new]
        );
    }
    
    protected static function log(
        int $user_id,
        EventType $event_type,
        string $object_name,
        ObjectType $object_type,
        string $parent,
        array $additional_data
    ) {
        $entry = new EventLogEntryAR();
        $entry->setTimestamp(date('Y-m-d H:i:s', time()));
        $entry->setEventType($event_type);
        $entry->setUserId($user_id);
        $entry->setObjectName($object_name);
        $entry->setObjectType($object_type);
        $entry->setParent($parent);
        $entry->setAdditionalData($additional_data);
        $entry->create();
    }
}
