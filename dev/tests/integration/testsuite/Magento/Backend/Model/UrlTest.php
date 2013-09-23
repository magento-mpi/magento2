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
 * Test class for Magento_Backend_Model_Url.
 *
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Model_UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Url
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Backend_Model_Url');
    }

    /**
     * @covers Magento_Backend_Model_Url::getSecure
     */
    public function testIsSecure()
    {
        Mage::app()->getStore()->setConfig('web/secure/use_in_adminhtml', true);
        $this->assertTrue($this->_model->isSecure());

        Mage::app()->getStore()->setConfig('web/secure/use_in_adminhtml', false);
        $this->assertFalse($this->_model->isSecure());

        $this->_model->setData('secure_is_forced', true);
        $this->_model->setData('secure', true);
        $this->assertTrue($this->_model->isSecure());

        $this->_model->setData('secure', false);
        $this->assertFalse($this->_model->isSecure());
    }

    /**
     * @covers Magento_Backend_Model_Url::getSecure
     */
    public function testSetRouteParams()
    {
        $this->_model->setRouteParams(array('_nosecret' => 'any_value'));
        $this->assertTrue($this->_model->getNoSecret());

        $this->_model->setRouteParams(array());
        $this->assertFalse($this->_model->getNoSecret());
    }

    /**
     * App isolation is enabled to protect next tests from polluted registry by getUrl()
     *
     * @covers Magento_Backend_Model_Url::getSecure
     * @magentoAppIsolation enabled
     */
    public function testGetUrl()
    {
        $url = $this->_model->getUrl('adminhtml/auth/login');
        $this->assertContains('admin/auth/login/key/', $url);
    }

    /**
     * @param string $routeName
     * @param string $controller
     * @param string $action
     * @param string $expectedHash
     * @dataProvider getSecretKeyDataProvider
     * @magentoAppIsolation enabled
     */
    public function testGetSecretKey($routeName, $controller, $action, $expectedHash)
    {
        /** @var $request Magento_Core_Controller_Request_Http */
        $request = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Controller_Request_Http');
        $request->setControllerName('default_controller')
            ->setActionName('default_action')
            ->setRouteName('default_router');

        $this->_model->setRequest($request);
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Session')
            ->setData('_form_key', 'salt');
        $this->assertEquals($expectedHash, $this->_model->getSecretKey($routeName, $controller, $action));
    }

    /**
     * @return array
     */
    public function getSecretKeyDataProvider()
    {
        /** @var $helper Magento_Core_Helper_Data */
        $helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Helper_Data');
        return array(
            array('', '', '',
                $helper->getHash('default_router' . 'default_controller' . 'default_action' . 'salt')),
            array('', '', 'action',
                $helper->getHash('default_router' . 'default_controller' . 'action' . 'salt')),
            array('', 'controller', '',
                $helper->getHash('default_router' . 'controller' . 'default_action' . 'salt')),
            array('', 'controller', 'action',
                $helper->getHash('default_router' . 'controller' . 'action' . 'salt')),
            array('adminhtml', '', '',
                $helper->getHash('adminhtml' . 'default_controller' . 'default_action' . 'salt')),
            array('adminhtml', '', 'action',
                $helper->getHash('adminhtml' . 'default_controller' . 'action' . 'salt')),
            array('adminhtml', 'controller', '',
                $helper->getHash('adminhtml' . 'controller' . 'default_action' . 'salt')),
            array('adminhtml', 'controller', 'action',
                $helper->getHash('adminhtml' . 'controller' . 'action' . 'salt')),
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetSecretKeyForwarded()
    {
        /** @var $helper Magento_Core_Helper_Data */
        $helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Helper_Data');
        /** @var $request Magento_Core_Controller_Request_Http */
        $request = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Controller_Request_Http');
        $request->setControllerName('controller')->setActionName('action');
        $request->initForward()->setControllerName(uniqid())->setActionName(uniqid());
        $this->_model->setRequest($request);
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Session')
            ->setData('_form_key', 'salt');
        $this->assertEquals(
            $helper->getHash('controller' . 'action' . 'salt'),
            $this->_model->getSecretKey()
        );
    }

    public function testUseSecretKey()
    {
        $this->_model->setNoSecret(true);
        $this->assertFalse($this->_model->useSecretKey());

        $this->_model->setNoSecret(false);
        $this->assertTrue($this->_model->useSecretKey());
    }
}
