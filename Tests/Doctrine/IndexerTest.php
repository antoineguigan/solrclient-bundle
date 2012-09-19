<?php
//TODO: move the tests to a different bundle, to enable the creation of Doctrine entities
namespace Qimnet\SolrClientBundle\Tests\Doctrine;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Qimnet\SolrClientBundle\Doctrine\Indexer;
use Qimnet\SolrClientTestBundle\Entity;
use Doctrine\ORM\EntityManager;
class IndexerTest extends WebTestCase 
{
    /**
     * @var Indexer $indexer
     */
    protected $indexer;
    /**
     * @var EntityManager $em
     */
    protected $em;
    /**
     * @var \SolrClient $client
     */
    protected $client;
    
    protected function setUp() {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->indexer = static::$kernel->getContainer()->get('qimnet.solr.indexer');
        $this->em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->client = self::$kernel->getContainer()->get('qimnet.solr.client');
    }
    public function testBatchUpdate()
    {
        $s = new Entity\TestBatchUpdate();
        $s->setContent('Lorem');
        $this->em->persist($s);
        $this->em->flush();
        $this->indexer->indexEntities($this->em, 'Qimnet\SolrClientTestBundle\Entity\TestBatchUpdate');
        
        $query = new \SolrQuery();
        $query->setQuery("id:" . $s->getSolrId());
        $response = $this->client->query($query)->getResponse();
        $this->assertEquals(1,$response->response->numFound);
        
        $s->setContent('Ipsum');
        $this->em->persist($s);
        $this->em->flush();
        $this->indexer->indexEntities($this->em, 'Qimnet\SolrClientTestBundle\Entity\TestBatchUpdate');
        $query = new \SolrQuery();
        $query->setQuery("content:Ipsum");
        $response = $this->client->query($query)->getResponse();
        $this->assertGreaterThanOrEqual(1,$response->response->numFound);
        
        $query = new \SolrQuery();
        $query->setQuery("id:". $s->getSolrId());
        $this->em->remove($s);
        $this->em->flush();
        $response = $this->client->query($query)->getResponse();
        $this->assertEquals(0,$response->response->numFound);
    }
    
    public function testAutoUpdate()
    {
        $s = new Entity\TestAutoUpdate();
        $s->setContent('Lorem');
        $this->em->persist($s);
        $this->em->flush();
        
        $query = new \SolrQuery();
        $query->setQuery("id:" . $s->getSolrId());
        $response = $this->client->query($query)->getResponse();
        $this->assertEquals(1,$response->response->numFound);
        
        $s->setContent('Ipsum');
        $this->em->persist($s);
        $this->em->flush();
        $query = new \SolrQuery();
        $query->setQuery("content:Ipsum");
        $response = $this->client->query($query)->getResponse();
        $this->assertGreaterThanOrEqual(1,$response->response->numFound);
        
        $query = new \SolrQuery();
        $query->setQuery("id:". $s->getSolrId());
        $this->em->remove($s);
        $this->em->flush();
        $response = $this->client->query($query)->getResponse();
        $this->assertEquals(0,$response->response->numFound);
    }
}

?>
