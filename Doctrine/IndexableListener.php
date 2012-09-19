<?php
namespace Qimnet\SolrBundle\Doctrine;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Qimnet\SolrBundle\Doctrine\Indexer;
use Symfony\Component\DependencyInjection\ContainerInterface;

class IndexableListener implements EventSubscriber {
    /**
     * @var Indexer $indexer
     */
    protected $indexer;
    protected $indexable_ids=array();
    
    public function __construct(Indexer $indexer) {
        $this->indexer = $indexer;
    }
    public function getSubscribedEvents() {
        return array('prePersist', 'postPersist', 'preUpdate','postUpdate', 'preRemove');
    }
    protected function getEntityFields(LifecycleEventArgs $args)
    {
        return $this->indexer->getEntityFields(get_class($args->getEntity()));
    }
    protected function isIndexable(LifecycleEventArgs $args)
    {
        return count($this->getEntityFields($args)->indexable);
    }
    protected function isRealtime(LifecycleEventArgs $args)
    {
        return is_null($this->getEntityFields($args)->needs_index);
    }
    protected function getSolrId(LifecycleEventArgs $args)
    {
        return $this->indexer->getSolrId($args->getEntity());
    }
    public function prePersist(LifecycleEventArgs $args)
    {
        if ($this->isIndexable($args) && !$this->isRealtime($args))
        {
            $setter = $this->getEntityFields($args)->needs_index->setter;
            $args->getEntity()->$setter(1);
        }
    }
    public function postPersist(LifecycleEventArgs $args)
    {
        if ($this->isIndexable($args) && $this->isRealtime($args))
        {
            $this->indexer->indexEntity($args->getEntity());
        }
    }
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $modified = false;
        foreach( $this->getEntityFields($args)->indexable as $indexable)
        {
            if ($indexable->field && $args->hasChangedField($indexable->field))
            {
                $modified = true;
                break;
            }
        }
        if ($modified)
        {
            if ($this->isRealtime($args))
            {
                $this->indexable_ids[] = $this->getSolrId($args);
            }
            else
            {
                $setter = $this->getEntityFields($args)->needs_index->setter;
                $args->getEntity()->$setter(1);
            }
        }
    }
    public function postUpdate(LifecycleEventArgs $args)
    {
        if (null !== ($pos = array_search($this->getSolrId($args),$this->indexable_ids)))
        {
            $this->indexer->indexEntity($args->getEntity());
            unset($this->indexable_ids[$pos]);
        }
    }
    public function preRemove(LifecycleEventArgs $args)
    {
        $this->indexer->removeEntity($args->getEntity());
    }
}

?>
