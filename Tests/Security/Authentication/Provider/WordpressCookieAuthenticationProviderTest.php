<?php

namespace Hypebeast\WordpressBundle\Tests\Security\Authentication\Provider;

use Hypebeast\WordpressBundle\Security\Authentication\Provider\WordpressCookieAuthenticationProvider;
use Symfony\Component\Security\Core\Role\Role;

/**
 * Test class for WordpressCookieAuthenticationProvider.
 * Generated by PHPUnit on 2011-09-29 at 14:41:47.
 * 
 * @covers Hypebeast\WordpressBundle\Security\Authentication\Provider\WordpressCookieAuthenticationProvider
 */
class WordpressCookieAuthenticationProviderTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var WordpressCookieAuthenticationProvider
     */
    protected $object;
    
    /**
     *
     * @var Hypebeast\WordpressBundle\Wordpress\ApiAbstraction
     */
    protected $api;
    
    /*
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->api = $this->getMockBuilder('Hypebeast\\WordpressBundle\\Wordpress\\ApiAbstraction')
                ->disableOriginalConstructor()->setMethods(array('wp_get_current_user'))->getMock();
        
        $this->object = new WordpressCookieAuthenticationProvider($this->api);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    public function testAuthenticateLoggedInUser() {
        $user = $this->getMock('WP_User');
        $user->ID = 99;
        $user->user_login = 'someuser';
        $user->roles = array('somerole', 'anotherrole');

        $this->api->expects($this->once())->method('wp_get_current_user')
                ->will($this->returnValue($user));

        $result = $this->object->authenticate($this->getMock(
                'Hypebeast\\WordpressBundle\\Security\\Authentication\\Token\\WordpressCookieToken'));
        
        # We should get back a WordpressCookieToken, be marked as authenticated, and be set as the user
        $this->assertInstanceOf(
                'Hypebeast\\WordpressBundle\\Security\\Authentication\\Token\\WordpressCookieToken',
                $result
        );
        $this->assertTrue($result->isAuthenticated());
        $this->assertEquals($user->ID, $result->getUser()->ID);
        $this->assertEquals($user->user_login, $result->getUsername());
        $this->assertEquals(
                array(new Role('ROLE_WP_SOMEROLE'), new Role('ROLE_WP_ANOTHERROLE')),
                $result->getRoles()
        );
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function testAuthenticateNotLoggedInUser() {
        $user = $this->getMock('WP_User');
        $user->ID = 0;
        
        $this->api->expects($this->once())->method('wp_get_current_user')
                ->will($this->returnValue($user));
        
        $this->object->authenticate($this->getMock(
                'Hypebeast\\WordpressBundle\\Security\\Authentication\\Token\\WordpressCookieToken'));
    }

    public function testSupports() {
        $this->assertTrue($this->object->supports(
                new \Hypebeast\WordpressBundle\Security\Authentication\Token\WordpressCookieToken));
        
        $this->assertFalse($this->object->supports($this->getMockForAbstractClass(
                'Symfony\\Component\\Security\\Core\\Authentication\\Token\\AbstractToken')));
    }
}

?>
