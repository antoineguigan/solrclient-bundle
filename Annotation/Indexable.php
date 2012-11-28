<?php
namespace Qimnet\SolrClientBundle\Annotation;
/**
 * Used to index Doctrine entities with Solr.
 * Can be applied to methods or properties of Doctrine entities.
 * 
 * @Annotation
 */
class Indexable 
{
    /**
     * Set to true if the field or the method is the id field
     * @var boolean $id
     */
    public $id=false;
    /**
     * The name of the field in the solr index. This attribute is required if 
     * the annotation is applied to a method.
     * 
     * @var string $solr_name
     */
    public $solr_name="";
    /**
     * The boost factor of the solr field.
     * 
     * @var int $boost
     */
    public $boost=1;
    
    /**
     * True if the field should only be used to trigger reindexations.
     * 
     * @var boolean $virtual
     */
    public $virtual=false;
}

?>
