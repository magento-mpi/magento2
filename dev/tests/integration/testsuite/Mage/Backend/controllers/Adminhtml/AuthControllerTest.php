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
        $expected = Mage::getSingleton('Mage_Backend_Model_Url')->getUrl('adminhtml/dashboard');
        try {
            foreach ($this->getResponse()->getHeaders() as $header) {
                if ($header['name'] == 'Location') {
                    $this->assertStringStartsWith($expected, $header['value'], 'Incorrect startup page url');
                    throw new Exception('Correct');
                }
            }
            $this->fail('There is no redirection to startup page');
        } catch (Exception $e) {
            $this->assertEquals('Correct', $e->getMessage());
            $this->assertRedirect();
        }
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

        $this->dispatch('admin/index/index');

        $response = Mage::app()->getResponse();
        $code = $response->getHttpResponseCode();
        $this->assertTrue($code >= 300 && $code < 400, 'Incorrect response code');

        $this->assertTrue(Mage::getSingleton('Mage_Backend_Model_Auth')->isLoggedIn());
    }

    /**
     * Check login redirection
     * @covers Mage_Backend_Controller_ActionAbstract::_performLogin
     */
    public function testPerformLogin()
    {
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOffSecretKey();
        $postLogin = array('login' => array(
            'username' => Magento_Test_Bootstrap::ADMIN_NAME,
            'password' => Magento_Test_Bootstrap::ADMIN_PASSWORD
        ));
        $this->getRequest()->setPost($postLogin);

        $expected = Mage::getSingleton('Mage_Backend_Model_Url')->getUrl('adminhtml/system_account/index');
        $this->dispatch($expected);
        try {
            foreach ($this->getResponse()->getHeaders() as $header) {
                if ($header['name'] == 'Location') {
                    $this->assertContains('admin/system_account/index', $header['value'], 'Incorrect page url');
                    throw new Exception('Correct');
                }
            }
            $this->fail('There is no redirection to specified page');
        } catch (Exception $e) {
            $this->assertEquals('Correct', $e->getMessage());
            $this->assertRedirect();
        }
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOnSecretKey();
    }

    /**
     * @covers Mage_Backend_Adminhtml_AuthController::logoutAction
     */
    public function testLogoutAction()
    {
        $this->_login();
        $this->dispatch('admin/auth/logout');
        $this->assertRedirect(Mage::helper('Mage_Backend_Helper_Data')->getHomePageUrl());
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
        $data = array(
            'ajaxExpired' => 1,
            'ajaxRedirect' => Mage::helper('Mage_Backend_Helper_Data')->getHomePageUrl(),
        );
        $expected = json_encode($data);
        $this->assertEquals($expected, $this->getResponse()->getBody());
        $this->_logout();
    }

    /**
     * @covers Mage_Backend_Adminhtml_AuthController::deniedIframeAction
     * @covers Mage_Backend_Adminhtml_AuthController::_getDeniedIframe
     */
    public function testDeniedIframeAction()
    {
        $this->_login();
        $homeUrl = Mage::helper('Mage_Backend_Helper_Data')->getHomePageUrl();
        $this->dispatch('admin/auth/deniedIframe');
        $expected = '<script type="text/javascript">parent.window.location =';
        $this->assertStringStartsWith($expected, $this->getResponse()->getBody());
        $this->assertContains($homeUrl, $this->getResponse()->getBody());
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
        $this->dispatch('admin/auth/login');
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
