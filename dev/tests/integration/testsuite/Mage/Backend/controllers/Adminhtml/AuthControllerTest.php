<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Backend_Adminhtml_AuthController.
 *
 */
class Mage_Backend_Adminhtml_AuthControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_session;

    /**
     * @var Mage_Backend_Model_Auth
     */
    protected $_auth;

    /**
     * @var Mage_User_Model_User
     */
    protected static $_newUser;

    /**
     * Performs user login
     */
    protected  function _login()
    {
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOffSecretKey();

        $this->_auth = Mage::getSingleton('Mage_Backend_Model_Auth');
        $this->_auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
        $this->_session = $this->_auth->getAuthStorage();
    }

    /**
     * Performs user logout
     */
    protected function _logout()
    {
        $this->_auth->logout();
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOnSecretKey();
    }

    /**
     * Check not logged state
     * @covers Mage_Backend_Adminhtml_AuthController::loginAction
     */
    public function testNotLoggedLoginAction()
    {
        $this->dispatch('admin/auth/login');
        $this->assertFalse($this->getResponse()->isRedirect());
        $expected = 'Log in to Admin Panel';
        $this->assertContains($expected, $this->getResponse()->getBody(), 'There is no login form');
    }

    /**
     * Check logged state
     * @covers Mage_Backend_Adminhtml_AuthController::loginAction
     */
    public function testLoggedLoginAction()
    {
        $this->_login();
        $this->dispatch('admin/auth/login');
        $this->assertRedirect();
        $this->_logout();
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testNotLoggedLoginActionWithRedirect()
    {
        $this->getRequest()->setPost(array(
            'login' => array(
                'username' => Magento_Test_Bootstrap::ADMIN_NAME,
                'password' => Magento_Test_Bootstrap::ADMIN_PASSWORD,
            )
        ));

        $this->dispatch('admin/auth/index');

        $response = Mage::app()->getResponse();
        $code = $response->getHttpResponseCode();
        $this->assertTrue($code >= 300 && $code < 400);

        $this->assertTrue(Mage::getSingleton('Mage_Backend_Model_Auth')->isLoggedIn());
    }

    /**
     * @covers Mage_Backend_Adminhtml_AuthController::logoutAction
     */
    public function testLogoutAction()
    {
        $this->_login();
        $this->dispatch('admin/auth/logout');
        $this->assertRedirect();
        $this->assertFalse($this->_session->isLoggedIn(), 'User is not logouted');
    }

    /**
     * @covers Mage_Backend_Adminhtml_AuthController::deniedJsonAction
     * @covers Mage_Backend_Adminhtml_AuthController::_getDeniedJson
     */
    public function testDeniedJsonAction()
    {
        $this->_login();
        $this->dispatch('admin/auth/deniedJson');
        $expected = '{"ajaxExpired":1,"ajaxRedirect":"http';
        $this->assertStringStartsWith($expected, $this->getResponse()->getBody());
        $this->_logout();
    }

    /**
     * @covers Mage_Backend_Adminhtml_AuthController::deniedIframeAction
     * @covers Mage_Backend_Adminhtml_AuthController::_getDeniedIframe
     */
    public function testDeniedIframeAction()
    {
        $this->_login();
        $this->dispatch('admin/auth/deniedIframe');
        $expected = '<script type="text/javascript">parent.window.location =';
        $this->assertStringStartsWith($expected, $this->getResponse()->getBody());
        $this->_logout();
    }

    /**
     * Test user logging process when user not assigned to any role
     * @dataProvider incorrectLoginDataProvider
     * @magentoDataFixture userDataFixture
     *
     * @param $params
     */
    public function testIncorrectLogin($params)
    {
        $this->getRequest()->setPost($params);
        $this->dispatch('admin/auth/index');
        $this->assertContains('Invalid User Name or Password', $this->getResponse()->getBody());
    }

    public static function userDataFixture()
    {
        self::$_newUser = new Mage_User_Model_User;
        self::$_newUser->setFirstname('admin_role')
            ->setUsername('test2')
            ->setPassword('123123q')
            ->setIsActive(1)
            ->save();

        self::$_newUser = new Mage_User_Model_User;
        self::$_newUser->setFirstname('admin_role')
            ->setUsername('test3')
            ->setPassword('123123q')
            ->setIsActive(0)
            ->setRoleId(1)
            ->save();
    }

    public function incorrectLoginDataProvider()
    {
        return array(
            'login dummy user' => array (
                array(
                    'login' => array(
                        'username' => 'test1',
                        'password' => '123123q',
                    )
                ),
            ),
            'login without role' => array (
                array(
                    'login' => array(
                        'username' => 'test2',
                        'password' => '123123q',
                    )
                ),
            ),
            'login not active user' => array (
                array(
                    'login' => array(
                        'username' => 'test3',
                        'password' => '123123q',
                    )
                ),
            ),
        );
    }
}
