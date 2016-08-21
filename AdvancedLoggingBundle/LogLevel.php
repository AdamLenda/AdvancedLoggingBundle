<?php
namespace AdvancedLoggingBundle;

class LogLevel
{
    const DEBUG = 10;
    const INFO = 20;
    const WARN = 30;
    const ERROR = 40;
    const ALERT = 50;

    private static $TYPE_NAMES = [
        self::DEBUG => 'DEBUG',
        self::INFO => 'INFO',
        self::WARN => 'WARN',
        self::ERROR => 'ERROR',
        self::ALERT => 'ALERT'
    ];

    /**
     * @param $typeId
     * @return string
     * @throws \Exception
     */
    public static function getTypeName($typeId)
    {
        if (!array_key_exists($typeId, self::$TYPE_NAMES)) {
            throw new \Exception('Unknown Log Level Type Id: ' . $typeId);
        }
        return self::$TYPE_NAMES[$typeId];
    }

    /**
     * @param $typeName
     * @return int|null
     */
    public static function getIdByName($typeName)
    {
        foreach (self::$TYPE_NAMES as $key => $value) {
            if (strcasecmp(strtolower($value), strtolower($typeName)) == 0) {
                return $key;
            }
        }
        return null;
    }
}