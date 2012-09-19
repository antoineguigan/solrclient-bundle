<?php
namespace Qimnet\SolrBundle\Tests\Doctrine;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Qimnet\SolrBundle\Doctrine\Indexer;
use Qimnet\CombatBundle\Entity\Story;
use Qimnet\CombatBundle\Entity\Tag;
/**
 *
 * @author akton
 */
class IndexerTest extends WebTestCase 
{
    /**
     * @var Indexer $indexer
     */
    protected $indexer;
    protected function setUp() {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->indexer = static::$kernel->getContainer()->get('qimnet.solr.indexer');
    }
    public function testEntityFields()
    {
        
        $this->assertInstanceOf('stdClass',$this->indexer->getEntityFields('Qimnet\CombatBundle\Entity\Story'));
    }
    public function testIndex()
    {
        $s = new Story;
        $s->setContent('haha');
        $tag = new Tag();
        $tag->setName('test');
        $s->addTag($tag);
        $tag = new Tag();
        $tag->setName('test2');
        $s->addTag($tag);
        $em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $em->persist($s);
        $em->flush();
        $indexer = self::$kernel->getContainer()->get('qimnet.solr.indexer');
        $indexer->indexEntities($em, 'Qimnet\CombatBundle\Entity\Story');
        $client = self::$kernel->getContainer()->get('qimnet.solr.client');
        $query = new \SolrQuery();
        $query->setQuery("id:" . $s->getId());
        $response = $client->query($query)->getResponse();
        $this->assertEquals(1,$response->response->numFound);
        $s->setContent('hello');
        $em->persist($s);
        $em->flush();
        $indexer->indexEntities($em, 'Qimnet\CombatBundle\Entity\Story');
        $query = new \SolrQuery();
        $query->setQuery("content:hello");
        $response = $client->query($query)->getResponse();
        $this->assertGreaterThanOrEqual(1,$response->response->numFound);
        $query = new \SolrQuery();
        $query->setQuery("id:". $s->getId());
        $em->remove($s);
        $em->flush();
        $response = $client->query($query)->getResponse();
        $this->assertEquals(0,$response->response->numFound);
    }
}

?>
