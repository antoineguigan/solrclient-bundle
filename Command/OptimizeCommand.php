<?php
namespace Qimnet\SolrBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class OptimizeCommand extends ContainerAwareCommand {
    protected function configure() {
        $this
                ->setName('qimnet:solr:optimize')
                ->setDescription('Optimizes the solr index.');
                
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->getContainer()->get('qimnet.solr.client')->optimize();
    }
}

?>
