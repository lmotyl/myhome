<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Service\Purifier\Adapter;

/**
 *
 * @author motyl
 */
interface AdapterInterface
{
    public function payloadValidate($payload);

    public function fetchStatus();

    public function getPollutionRate();

    public function getTemperature();

    public function getHumidity();

    public function getMode();

    public function setMode($mode);

    public function getLevel();

    public function setLevel($level);

    public function powerOn();

    public function powerOff();

    public function getPowerState();

    public function getError();
}