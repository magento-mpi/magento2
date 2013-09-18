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
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Observer
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('Magento\Backend\Model\Observer');
    }

    public function testActionPreDispatchAdminNotLogged()
    {
        $this->markTestSkipped('Skipped because of authentication process moved into base controller.');

        $request = Mage::app()->getRequest();
        $this->assertEmpty($request->getRouteName());
        $this->assertEmpty($request->getControllerName());
        $this->assertEmpty($request->getActionName());

        $observer = $this->_buildObserver();
        $this->_model->actionPreDispatchAdmin($observer);

        $this->assertEquals('adminhtml', $request->getRouteName());
        $this->assertEquals('auth', $request->getControllerName());
        $this->assertEquals('login', $request->getActionName());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testActionPreDispatchAdminLoggedRedirect()
    {
        $this->markTestSkipped('Skipped because of authentication process moved into base controller.');

        $observer = $this->_buildObserver();
        $this->_model->actionPreDispatchAdmin($observer);

        $response = Mage::app()->getResponse();
        $code = $response->getHttpResponseCode();
        $this->assertTrue($code >= 300 && $code < 400);

        $session = Mage::getSingleton('Magento\Backend\Model\Auth\Session');
        $this->assertTrue($session->isLoggedIn());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store admin/security/use_form_key 0
     */
    public function testActionPreDispatchAdminLoggedNoRedirect()
    {
        $this->markTestSkipped('Skipped because of authentication process moved into base controller.');

        $observer = $this->_buildObserver();
        $this->_model->actionPreDispatchAdmin($observer);

        $response = Mage::app()->getResponse();
        $code = $response->getHttpResponseCode();
        $this->assertFalse($code >= 300 && $code < 400);

        $session = Mage::getSingleton('Magento\Backend\Model\Auth\Session');
        $this->assertTrue($session->isLoggedIn());
    }

    /**
     * Builds a dummy observer for testing adminPreDispatch method
     *
     * @return \Magento\Object
     */
    protected function _buildObserver()
    {
        $request = Mage::app()->getRequest();
        $request->setPost(
            'login',
            array(
                'username' => Magento_TestFramework_Bootstrap::ADMIN_NAME,
                'password' => Magento_TestFramework_Bootstrap::ADMIN_PASSWORD
            )
        );

        $controller = new \Magento\Object(array('request' => $request));
        $event = new \Magento\Object(array('controller_action' => $controller));
        $observer = new \Magento\Object(array('event' => $event));
        return $observer;
    }
}
