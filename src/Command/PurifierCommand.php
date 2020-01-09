<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\Purifier\PurifierService;

class PurifierCommand extends \Symfony\Component\Console\Command\Command
{
    protected static $defaultName = 'app:purifier';

    private $params;

    /**
     *
     * @var PurifierService;
     */
    private $purifierService;

    public function __construct(
        string $name = null,
        ParameterBagInterface $params,
        PurifierService $purifierService
    ) {
        $this->params = $params;
        $this->purifierService = $purifierService;
        parent::__construct($name);
    }


    protected function configure()
    {
        
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // ...
        $this->purifierService->initAdapter();
        $this->purifierService->execute();
        return 0;
    }
}