<?php

namespace App\Service\Purifier;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PurifierService
{

    private $params;

    private $general;

    private $devices;

    /**
     *
     * @var Adapter\AdapterInterface
     */
    private $purifier;

    public function __construct(
        ParameterBagInterface $params
    )
    {
        $this->params = $params;
        $this->extractParams();
    }

    private function extractParams()
    {
        $config = $this->params->get('purifier');

        $this->general = $config['general'] ?? null;
        $this->devices = $config['devices'] ?? null;
    }

    public function initAdapter($index = 0)
    {
        if (!isset($this->devices[$index])) {
            throw new \Exception(sprintf('Config for device %i is undefined', $index));
        }

        $adapter = $this->devices[$index]['adapter'] ?? null;
        $payload = $this->devices[$index]['payload'] ?? null;

        if (is_null($adapter) || !class_exists($adapter)) {
            throw new \Exception(sprintf('Adapter Class is undefined!'));
        }

        $this->purifier = new $adapter($payload);
    }


    public function execute()
    {
        while (true) {
            echo $this->getPollutionString($this->purifier->getPollutionRate());
            sleep($this->general['delay']);
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