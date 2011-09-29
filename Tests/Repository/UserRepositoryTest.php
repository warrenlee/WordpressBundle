<?php

namespace Hypebeast\WordpressBundle\Tests\Repository;

use Hypebeast\WordpressBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;

/**
 * Test class for UserRepository.
 * Generated by PHPUnit on 2011-09-29 at 14:47:13.
 */
class UserRepositoryTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var UserRepository
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    public function testFindOneByWithUsername() {
        $entity_manager = $this->getMockBuilder('Doctrine\\ORM\\EntityManager')
                ->disableOriginalConstructor()->getMock();
        
        $user_repo = new UserRepository(
                $entity_manager,
                $this->getMockBuilder('Doctrine\\ORM\\Mapping\\ClassMetadata')
                        ->disableOriginalConstructor()->getMock()
        );
        
        $query = $this->getMockBuilder('Hypebeast\\WordpressBundle\\Tests\\Repository\\MockQuery')
                ->disableOriginalConstructor()->getMock();
        $expected_username = 'jbloggs';
        
        $entity_manager->expects($this->once())->method('createQuery')
                ->with($this->logicalAnd(
                        $this->stringContains('SELECT user, meta'),
                        $this->stringContains('FROM HypebeastWordpressBundle:User user'),
                        $this->stringContains('JOIN user.metas meta WHERE user.username = :username')
                ))->will($this->returnValue($query));
        
        $query->expects($this->once())->method('setParameter')
                ->with($this->equalTo('username'), $this->equalTo($expected_username))
                ->will($this->returnValue($query));
        
        $query->expects($this->once())->method('getSingleResult')->with()
                ->will($this->returnValue('expected return'));
        
        $this->assertEquals('expected return', $user_repo->findOneBy(
                array('username' => $expected_username, 'unexpected_param' => 'unexpected_value')));
    }
    
    public function testFindOneByWithoutUsername() {
        $criteria = array('criterion' => 'value', 'another criterion' => 'some other value');
        
        $user_repo = $this->getMockBuilder('Hypebeast\\WordpressBundle\\Repository\\UserRepository')
                ->disableOriginalConstructor()->getMock();
        $user_repo->expects($this->once())->method('findOneBy')->with($criteria)
                ->will($this->returnValue('expected return'));
        
        $this->assertEquals('expected return', $user_repo->findOneBy($criteria));
    }

}

use Doctrine\ORM\AbstractQuery;

class MockQuery extends AbstractQuery {
    public function getSql() { }
    protected function _doExecute() { }
}