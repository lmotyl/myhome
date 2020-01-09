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

    private function fetchStatus() {

        if ($this->statusFetchedAt >= time()) {
            return null;
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
        
    }

    public function getMode()
    {

    }

    public function getPollutionRate()
    {
        $this->fetchStatus();
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

    public function setLevel($level)
    {
        
    }

    public function setMode($mode)
    {

    }
}