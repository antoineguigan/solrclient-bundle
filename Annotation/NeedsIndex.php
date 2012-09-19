<?php
namespace Qimnet\SolrClientBundle\Annotation;
/**
 * If a property of a Doctrine entity is marked with this annotation, it will
 * not be indexed directly upon insertion or update.
 * 
 * Manual indexation of entities can be achieved with the qimnet.solr.indexer
 * service, or with the qimnet:solr:index task.
 * 
 * @Annotation
 */
class NeedsIndex {
}

?>
