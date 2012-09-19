<?php
namespace Qimnet\SolrBundle\Annotation;
/**
 * @Annotation
 */
class Indexable 
{
    public $id=false;
    public $solr_name="";
    public $boost=1;
}

?>
