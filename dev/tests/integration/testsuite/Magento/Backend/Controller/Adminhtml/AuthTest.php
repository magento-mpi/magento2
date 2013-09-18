<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Backend_Controller_Adminhtml_Auth
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Controller_Adminhtml_AuthTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_session;

    /**
     * @var Magento_Backend_Model_Auth
     */
    protected $_auth;

    protected function tearDown()
    {
        $this->_session = null;
        $this->_auth = null;
        parent::tearDown();
    }

    /**
     * Performs user login
     */
    protected  function _login()
    {
        Mage::getSingleton('Magento_Backend_Model_Url')->turnOffSecretKey();

        $this->_auth = Mage::getSingleton('Magento_Backend_Model_Auth');
        $this->_auth->login(
            Magento_TestFramework_Bootstrap::ADMIN_NAME, Magento_TestFramework_Bootstrap::ADMIN_PASSWORD);
        $this->_session = $this->_auth->getAuthStorage();
    }

    /**
     * Performs user logout
     */
    protected function _logout()
    {
        $this->_auth->logout();
        Mage::getSingleton('Magento_Backend_Model_Url')->turnOnSecretKey();
    }

    /**
     * Check not logged state
     * @covers Magento_Backend_Controller_Adminhtml_Auth::loginAction
     */
    public function testNotLoggedLoginAction()
    {
        $this->dispatch('backend/admin/auth/login');
        $this->assertFalse($this->getResponse()->isRedirect());

        $body = $this->getResponse()->getBody();
        $this->assertSelectCount('form#login-form input#username[type=text]', true, $body);
        $this->assertSelectCount('form#login-form input#login[type=password]', true, $body);
    }

    /**
     * Check logged state
     * @covers Magento_Backend_Controller_Adminhtml_Auth::loginAction
     * @magentoDbIsolation enabled
     */
    public function testLoggedLoginAction()
    {
        $this->_login();

        $this->dispatch('backend/admin/auth/login');
        /** @var $backendUrlModel Magento_Backend_Model_Url */
        $backendUrlModel = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Backend_Model_Url');
        $url = $backendUrlModel->getStartupPageUrl();
        $expected = $backendUrlModel->getUrl($url);
        $this->assertRedirect($this->stringStartsWith($expected));

        $this->_logout();
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testNotLoggedLoginActionWithRedirect()
    {
        $this->getRequest()->setPost(array(
            'login' => array(
                'username' => Magento_TestFramework_Bootstrap::ADMIN_NAME,
                'password' => Magento_TestFramework_Bootstrap::ADMIN_PASSWORD,
            )
        ));

        $this->dispatch('backend/admin/index/index');

        $response = Mage::app()->getResponse();
        $code = $response->getHttpResponseCode();
        $this->assertTrue($code >= 300 && $code < 400, 'Incorrect response code');

        $this->assertTrue(Mage::getSingleton('Magento_Backend_Model_Auth')->isLoggedIn());
    }

    /**
     * @covers Magento_Backend_Controller_Adminhtml_Auth::logoutAction
     * @magentoDbIsolation enabled
     */
    public function testLogoutAction()
    {
        $this->_login();
        $this->dispatch('backend/admin/auth/logout');
        $this->assertRedirect(
            $this->equalTo(Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                    ->get('Magento_Backend_Helper_Data')
                ->getHomePageUrl()
            )
        );
        $this->assertFalse($this->_session->isLoggedIn(), 'User is not logged out.');
    }

    /**
     * @covers Magento_Backend_Controller_Adminhtml_Auth::deniedJsonAction
     * @covers Magento_Backend_Controller_Adminhtml_Auth::_getDeniedJson
     * @magentoDbIsolation enabled
     */
    public function testDeniedJsonAction()
    {
        $this->_login();
        $this->dispatch('backend/admin/auth/deniedJson');
        $data = array(
            'ajaxExpired' => 1,
            'ajaxRedirect' => Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                ->get('Magento_Backend_Helper_Data')
                ->getHomePageUrl(),
        );
        $expected = json_encode($data);
        $this->assertEquals($expected, $this->getResponse()->getBody());
        $this->_logout();
    }

    /**
     * @covers Magento_Backend_Controller_Adminhtml_Auth::deniedIframeAction
     * @covers Magento_Backend_Controller_Adminhtml_Auth::_getDeniedIframe
     * @magentoDbIsolation enabled
     */
    public function testDeniedIframeAction()
    {
        $this->_login();
        $this->dispatch('backend/admin/auth/deniedIframe');
        $homeUrl = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Backend_Helper_Data')
            ->getHomePageUrl();
        $expected = '<script type="text/javascript">parent.window.location =';
        $this->assertStringStartsWith($expected, $this->getResponse()->getBody());
        $this->assertContains($homeUrl, $this->getResponse()->getBody());
        $this->_logout();
    }

    /**
     * Test user logging process when user not assigned to any role
     * @dataProvider incorrectLoginDataProvider
     * @magentoDbIsolation enabled
     *
     * @param $params
     */
    public function testIncorrectLogin($params)
    {
        $this->getRequest()->setPost($params);
        $this->dispatch('backend/admin/auth/login');
        $this->assertContains('Please correct the user name or password.', $this->getResponse()->getBody());
    }

    public function incorrectLoginDataProvider()
    {
        return array(
            'login dummy user' => array (
                array(
                    'login' => array(
                        'username' => 'test1',
                        'password' => Magento_TestFramework_Bootstrap::ADMIN_PASSWORD,
                    )
                ),
            ),
            'login without role' => array (
                array(
                    'login' => array(
                        'username' => 'test2',
                        'password' => Magento_TestFramework_Bootstrap::ADMIN_PASSWORD,
                    )
                ),
            ),
            'login not active user' => array (
                array(
                    'login' => array(
                        'username' => 'test3',
                        'password' => Magento_TestFramework_Bootstrap::ADMIN_PASSWORD,
                    )
                ),
            ),
        );
    }
}
