<?php

namespace App\Service\Purifier\PythonMiio;

use App\Service\Purifier\Adapter\Adapter;

class PythonMiioAdapter extends Adapter
{
    private $ip;

    private $token;

    private $status = [];

    private $statusFetchedAt = 0;


    public function __construct($payload)
    {
        $this->ip = $payload['ip'] ?? null;
        $this->token = $payload['token'] ?? null;
    }

    public function fetchStatus() {

        if ($this->statusFetchedAt >= time()) {
            return $this->status;
        }
        $this->statusFetchedAt = time();

        $query = sprintf(
            'miiocli airpurifier --ip %s --token %s status',
            $this->ip,
            $this->token
        );

        exec($query, $output);

        if (is_array($output)) {
            $this->status = PythonMiioParser::parse($output);
        }

        return $this->status;
    }


    public function payloadValidate($payload)
    {
        if (\is_null($this->ip)) {
            throw new \Exception('ip of device is not set');
        }

        if (\is_null($this->token)) {
            throw new \Exception('token of device is not set');
        }
    }

    public function getHumidity()
    {

    }

    public function getLevel()
    {
        return $this->status[PythonMiioParser::KEY_LEVEL] ?? null;
    }

    public function getMode()
    {
        return $this->status[PythonMiioParser::KEY_MODE] ?? null;
    }

    public function getPollutionRate()
    {
        return $this->status[PythonMiioParser::KEY_POLLUTION] ?? null;
    }

    public function getTemperature()
    {

    }

    public function powerOff()
    {
        
    }

    public function powerOn()
    {

    }

    public function getPowerState()
    {
        return $this->status[PythonMiioParser::KEY_POWER] ?? false;
    }

    public function setLevel($level)
    {
        $query = sprintf(
            'miiocli airpurifier --ip %s --token %s set_favorite_level %s',
            $this->ip,
            $this->token,
            $level
        );

        exec($query, $output);

    }

    public function setMode($mode)
    {

    }

    public function getError() {
        return $this->status[PythonMiioParser::KEY_ERROR] ?? false;
    }
}