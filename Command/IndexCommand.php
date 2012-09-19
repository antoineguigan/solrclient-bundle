<?php
namespace Qimnet\SolrBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
class IndexCommand extends ContainerAwareCommand {
    protected function configure() {
        $this
            ->setName('qimnet:solr:index')
            ->setDescription('Indexes Doctrines entities.')
            ->addArgument('entity',  InputArgument::IS_ARRAY, 'Entities that should be reindexed')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force reindexation')
            ->addOption('clear', 'c', InputOption::VALUE_NONE, 'Clears the index before indexation')
                ;
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        $indexer = $this->getContainer()->get('qimnet.solr.indexer');
        $client = $this->getContainer()->get('qimnet.solr.client');
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        if ($input->getOption('clear'))
        {
            $client->deleteByQuery("*:*");
            $client->commit();
        }
        if (!count($input->getArgument('entity')))
        {
            $indexer->indexAllEntities($em, $input->getOption('force'));
        }
        else
        {
            foreach($input->getArgument('entity') as $entity)
            {
                $entity = str_replace('/', '\\', $entity);
                $indexer->indexEntities($em, $entity, $input->getOption('force'));
            }
        }
    }
}

?>
