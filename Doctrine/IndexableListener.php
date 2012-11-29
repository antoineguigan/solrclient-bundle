<?php
namespace Qimnet\SolrClientBundle\Doctrine;
use Doctrine\Common\EventSubscriber;
use Qimnet\SolrClientBundle\Doctrine\Indexer;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Listens to Doctrine events and updates the Solr index accordingly.
 */
class IndexableListener implements EventSubscriber {
    /**
     * @var Indexer $indexer
     */
    protected $indexer;
    
    public function __construct(Indexer $indexer)
    {
        $this->indexer = $indexer;
    }
    public function getSubscribedEvents() 
    {
        return array('onFlush', 'postPersist');
    }
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($this->indexer->isIndexable($entity) && $this->indexer->isRealtime($entity))
        {
            $this->indexer->indexEntity($entity);
        }
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
            if (!$indexer->isRealtime($entity))
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
                $modified = false;
                $changeset = $uow->getEntityChangeSet($entity);
                foreach( $indexer->getEntityFields(get_class($entity))->indexable as $indexable)
                {
                    if ($indexable->field && array_key_exists($indexable->field, $changeset))
                    {
                        $modified = true;
                        break;
                    }
                }
                if ($modified || !$indexer->isIndexable($entity)) 
                {
                    $indexer->removeEntity($entity);
                }
                if ($modified) 
                {
                    $indexEntity($entity);
                }
            }
        }
    }

}

?>
