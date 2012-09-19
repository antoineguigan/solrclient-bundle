Qimnet SolrClientBundle
=======================

Used to integrate the *PECL SolrClient* extension into Symfony 2.


Features
--------

 * Automatic indexation and removal of Doctrine entities
 * Realtime indexation or batch indexation


Configuration
-------------

    qimnet_solr:          

        # list of entities that should be indexed by the batch command.
        entities:             [] 

        # see the SolrClient documentation on http://php.met for details about the options.
        client_options:       
            port:                 8080 
            hostname:             localhost 
            secure:               false 
            path:                 ~ 
            wt:                   ~ 
            login:                ~ 
            password:             ~ 
            proxy_host:           ~ 
            proxy_port:           ~ 
            proxy_login:          ~ 
            proxy_password:       ~ 
            timeout:              ~ 
            ssl_cert:             ~ 
            ssl_key:              ~ 
            ssl_keypassword:      ~ 
            ssl_cainfo:           ~ 
            ssl_capath:           ~ 


Doctrine Entity Annotations
---------------------------


Doctrine entities can be automatically inserted by using the following 
annotations.


        use Qimnet\QimnetSolrClientBundle\Annotation as Solr;

        class Entity
        {
            /**
            * @Solr\Indexable("id"=true)
            **/
            protected $id;
            /**
            * @Solr\Indexable
            **/
            protected $content;
            /**
            * @Solr\NeedsIndex
            **/
            protected $needs_index;
            /**
            * @Solr\Indexable(solr_name="tag")
            **/
            public function getTags() {
                return array("tag1", "tag2");
            }
        }


Batch index update
------------------


If no `@Solr\NeedsIndex` is found in the entity, the index is updated 
automatically.

To launch the indexation manually, use the `qimnet:solr:index` command.



Services
--------


The following services are defined :

    qimnet.solr.client                            container SolrClient
    qimnet.solr.indexable                         container Qimnet\SolrClientBundle\Doctrine\IndexableListener
    qimnet.solr.indexer                           container Qimnet\SolrClientBundle\Doctrine\Indexer



License
-------

**Qimnet SolrClient** is available under the MIT license.



copyright Antoine Guigan, 2012

[QIMNET](http://qimnet.com)
