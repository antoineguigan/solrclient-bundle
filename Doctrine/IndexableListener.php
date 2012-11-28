<?php
namespace Qimnet\SolrClientBundle\Doctrine;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Qimnet\SolrClientBundle\Doctrine\Indexer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
/**
 * Listens to Doctrine events and updates the Solr index accordingly.
 */
class IndexableListener implements EventSubscriber {
    /**
     * @var Indexer $indexer
     */
    protected $indexer;
    protected $indexable_ids=array();
    
    public function __construct(Indexer $indexer)
    {
        $this->indexer = $indexer;
    }
    public function getSubscribedEvents() 
    {
        return array('onFlush');
    }
    public function onFlush(OnFlushEventArgs $args)
    {
        $indexer = $this->indexer;
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();
        
        foreach ($uow->getScheduledEntityDeletions() as $entity)
        {
            if ($indexer->isIndexable($entity))
            {
                $indexer->removeEntity($args->getEntity());
            }
        }
        $indexEntity = function($entity) use($indexer, $em, $uow) {
            if ($indexer->isRealtime($entity))
            {
                $indexer->indexEntity($entity);
            }
            else
            {
                $setter = $indexer->getEntityFields(get_class($entity))
                        ->needs_index->setter;
                $entity->$setter(true);
                $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(get_class($entity)), $entity);
            }
        };
        foreach ($uow->getScheduledEntityInsertions() as $entity)
        {
            if ($indexer->isIndexable($entity))
            {
                $indexEntity($entity);
            }
        }
        foreach ($uow->getScheduledEntityUpdates() as $entity)
        {
            if ($indexer->hasIndexableFields($entity))
            {
                $indexer->removeEntity($entity);
                if ($indexer->isIndexable($entity))
                {
                    $changeset = $uow->getEntityChangeSet($entity);
                    $modified = false;
                    foreach( $indexer->getEntityFields(get_class($entity))->indexable as $indexable)
                    {
                        if ($indexable->field && array_key_exists($indexable->field, $changeset))
                        {
                            $modified = true;
                            break;
                        }
                    }
                    if ($modified) {
                        $indexEntity($entity);
                    }
                }
            }
        }
    }

}

?>
