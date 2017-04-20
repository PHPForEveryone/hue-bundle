<?php

namespace HueBundle\Command;

use GuzzleHttp\Exception\ConnectException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use HueBundle\Services\BridgeFinder;

class HueBridgesFindCommand extends ContainerAwareCommand
{

    /**
     * Configure function
     */
    protected function configure()
    {
        $this
            ->setName('hue:bridges:find')
            ->setDescription('Finds all bridges.')
        ;
    }

    /**
     * Execute function
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var BridgeFinder $finder */
        $finder = $this->getContainer()->get('hue.bridge.finder');

        try {
            $bridges = $finder->getBridgesList();
        } catch (ConnectException $ex) {
            die('Request failed. Ensure that you have internet connection.');
        }

        $output->writeln('List of Bridges:');
        foreach ($bridges as $index => $bridge) {
            $nr = $index+1;
            $output->writeln("Bridge #{$nr}");
            $output->writeln($bridge);
        }
    }
}
