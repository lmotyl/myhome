<?php

namespace App\Service\Purifier;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class PurifierService
{

    const TREND_INCREASING = 'increasing';
    const TREND_DECREASING = 'decreasing';
    const TREND_STABLE = 'stable';

    private $params;

    private $general;

    private $devices;

    private $scenarios;

    private $config;

    private $scenario;

    private $model;
    /**
     *
     * @var Adapter\AdapterInterface
     */
    private $purifier;

    private $pollMeasure = [];

    private $avgItemsLengh;


    public function __construct(
        ParameterBagInterface $params
    )
    {
        $this->params = $params;
        $this->extractParams();
        $this->initAverageParams();
    }

    private function extractParams()
    {
        $config = $this->params->get('purifier');

        $this->general = $config['general'] ?? null;
        $this->devices = $config['devices'] ?? null;
        $this->config = $config['config'] ?? null;
        $this->scenarios = $config['scenarios'] ?? null;

    }

    private function initAverageParams()
    {
        $period = $this->general['period'] ?? 60;
        $interval = $this->general['interval'] ?? 3;
        $this->avgItemsLengh = (int) $period / $interval;
        for ($i = 0; $i < $this->avgItemsLengh; $i++) {
            array_push($this->pollMeasure, 0);
        }
    }

    private function initDeviceScenario($scenarioName = 'default') {
        $this->scenario = $this->scenarios[$scenarioName] ?? null;
    }

    private function initDeviceModel($model = null) {
        $this->model = $this->config[$model];
    }

    private function getLevelByPollutionRate($pollutionRate, $trend, $currentLevel)
    {
        $searchLevel = $currentLevel;
//        var_dump($this->scenario);
        foreach ($this->scenario[$trend] as $item) {
            switch ($trend) {
                case self::TREND_INCREASING:
                    if ($pollutionRate > (int) $item['pollution']) {
                        $searchLevel = $item['level'];
                    }
                    break;
                case self::TREND_DECREASING:
                    if ($pollutionRate <= (int) $item['pollution']) {
                        $searchLevel = $item['level'];
                    }
                    break;
            }
        }

        return $searchLevel;
    }

    private function calculateAvgPollution($pollutionRate = null)
    {
        if (!is_null($pollutionRate)) {
            array_push($this->pollMeasure, $pollutionRate);
            if (count($this->pollMeasure) > $this->avgItemsLengh) {
                array_shift($this->pollMeasure);
            }
        }

        return (int) (array_sum($this->pollMeasure) / count($this->pollMeasure));
    }

    public function getPollTrend($prevAvg, $currAvg)
    {
        if ($currAvg < $prevAvg) {
            return self::TREND_DECREASING;
        }

        return self::TREND_INCREASING;
    }

    public function initAdapter($index = 0)
    {
        if (!isset($this->devices[$index])) {
            throw new \Exception(sprintf('Config for device %i is undefined', $index));
        }

        $adapter = $this->devices[$index]['adapter'] ?? null;
        $payload = $this->devices[$index]['payload'] ?? null;
        $scenarioName = $this->devices[$index]['scenario'] ?? 'default';

        if (is_null($adapter) || !class_exists($adapter)) {
            throw new \Exception(sprintf('Adapter Class is undefined!'));
        }

        $this->purifier = new $adapter($payload);
        $this->initDeviceScenario($scenarioName);
        $this->initDeviceModel($this->devices[$index]['model']);
    }


    public function mapLevel($level) {
        $key = array_search($level, $this->model['level_map']);
        return $key ?: Adapter\Adapter::LEVEL_QUIET;
    }

    public function execute()
    {
        while (true) {
            sleep($this->general['delay']);
            $status = $this->purifier->fetchStatus();
            if (false !== $this->purifier->getError()) {
                print_r([date('Y-m-d H:i:s'), $this->purifier->getError()]);
                continue;
            }

           
            $powerState = $this->purifier->getPowerState();

            if (false === $powerState) {
                print_r([date('Y-m-d H:i:s'), 'Power Off']);
                continue;
            }

            $pollRate = $this->purifier->getPollutionRate();
            $mode = $this->purifier->getMode();
            $currentLevel = $this->mapLevel($this->purifier->getLevel());
            if (PythonMiio\PythonMiioAdapter::MODE_MANUAL !== $mode) {
                continue;
            }

            $prevAvg = $this->calculateAvgPollution();
            $currAvg = $this->calculateAvgPollution($pollRate);
            $trend = $this->getPollTrend($prevAvg, $currAvg);

            $newLevel = $this->getLevelByPollutionRate($currAvg, $trend, $currentLevel);
            print_r([date('H:i:s'),$pollRate, implode(',', $this->pollMeasure), $prevAvg, $currAvg, $trend, $currentLevel, $newLevel]);

            switch ($trend) {
                case self::TREND_INCREASING:
                    if ($this->model['level_map'][$newLevel] > $this->model['level_map'][$currentLevel]) {
                        $this->purifier->setLevel($this->model['level_map'][$newLevel]);
                    }

                    break;
                case self::TREND_DECREASING:
                    if ($this->model['level_map'][$newLevel] < $this->model['level_map'][$currentLevel]) {
                        $this->purifier->setLevel($this->model['level_map'][$newLevel]);
                    }

                    break;
            }
        }
    }


    public function getPollutionString($pollutionRate)
    {
        return sprintf("Device: Pollution: %s \r", $pollutionRate);
    }

    public function isAvailable()
    {

    }
}