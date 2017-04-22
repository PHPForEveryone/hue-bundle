<?php

namespace HueBundle\Command;

use HueBundle\Services\BridgeFinder;
use HueBundle\Services\BridgeUser;
use HueBundle\Services\HueSession;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HueBridgeUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('hue:bridge:user')
            ->setDescription('Creates an user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var BridgeFinder $finder */
        $finder = $this->getContainer()->get('hue.bridge.finder');
        $bridge = $finder->getBridgesList()->current();

        /** @var HueSession $session */
        $session = $this->getContainer()->get('hue.session');
        $session->setHost($bridge->internalipaddress);

        /** @var BridgeUser $user */
        $user = $this->getContainer()->get('hue.bridge.user');
        $user->createUser();

        $error = $session->getSession()->getFlashBag()->get('error');
        var_dump($error);

        $output->writeln('Command result.');
    }
}
