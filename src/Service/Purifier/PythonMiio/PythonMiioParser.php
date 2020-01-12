<?php

namespace App\Service\Purifier\PythonMiio;

class PythonMiioParser
{

    const PARSE_POWER = 'Power:';
    const PARSE_POLLUTION = 'AQI:';
    const PARSE_TEMPERATURE = 'Temperature:';
    const PARSE_HUMIDITY = 'Humidity:';
    const PARSE_MODE = 'Mode:';
    const PARSE_FAVORITE_LEVEL = 'Favorite level:';
    const PARSE_ERROR = 'Error:';

    const KEY_POWER = 'power';
    const KEY_POLLUTION = 'pollution';
    const KEY_TEMPERATURE = 'temperature';
    const KEY_HUMIDITY = 'humidity';
    const KEY_MODE = 'mode';
    const KEY_LEVEL = 'level';
    const KEY_ERROR = 'error';

    const MAP_PARSER = [
       self::KEY_POWER => self::PARSE_POWER,
       self::KEY_POLLUTION => self::PARSE_POLLUTION,
       self::KEY_TEMPERATURE => self::PARSE_TEMPERATURE,
       self::KEY_HUMIDITY => self::PARSE_HUMIDITY,
       self::KEY_MODE => self::PARSE_MODE,
       self::KEY_LEVEL => self::PARSE_FAVORITE_LEVEL,
       self::KEY_ERROR => self::PARSE_ERROR
    ];

    public static function parse($payload)
    {
        $out = [];
        foreach($payload as $row) {
            foreach(self::MAP_PARSER as $key => $parse) {
                if (0 === strpos($row, $parse)) {
                    $out[$key] = trim(substr($row, strlen($parse)));
                }
            }
        }

        foreach($out as $key => $value) {
            $method = 'get'.ucfirst($key);
            $out[$key] = self::$method($value);
        }

        return $out;
    }

    public static function getError($value)
    {
        return $value;
    }

    public static function getPower($value)
    {
        switch($value) {
            case 'on': return true;
            case 'off' : return false;
            default: throw new \Exception(sprintf("Unrecognized power valuer: %s", $value));
        }
    }

    public static function getPollution($value)
    {
        return (int) $value;
    }

    public static function getTemperature($value)
    {
        return floatval($value);
    }

    public static function getHumidity($value)
    {
        return (int) $value;
    }

    public static function getMode($value)
    {
        switch($value) {
            case 'favorite' : return \App\Service\Purifier\Adapter\Adapter::MODE_MANUAL;
            case 'silent':
            case 'auto' : return \App\Service\Purifier\Adapter\Adapter::MODE_AUTO;
            default: throw new \Exception(sprintf("Unrecognized mode value: %s", $value));
        }
    }

    public static function getLevel($value)
    {
        return (int) $value;
    }

}