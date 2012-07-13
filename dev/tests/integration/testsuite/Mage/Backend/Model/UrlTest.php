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
 * Test class for Mage_Backend_Model_Url.
 */
class Mage_Backend_Model_UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Url
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Backend_Model_Url;
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * @covers Mage_Backend_Model_Url::getSecure
     */
    public function testGetSecure()
    {
        Mage::app()->getStore()->setConfig('web/secure/use_in_adminhtml', true);
        $this->assertTrue($this->_model->getSecure());

        Mage::app()->getStore()->setConfig('web/secure/use_in_adminhtml', false);
        $this->assertFalse($this->_model->getSecure());

        $this->_model->setData('secure_is_forced', true);
        $this->_model->setData('secure', true);
        $this->assertTrue($this->_model->getSecure());

        $this->_model->setData('secure', false);
        $this->assertFalse($this->_model->getSecure());
    }

    /**
     * @covers Mage_Backend_Model_Url::getSecure
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
     * @covers Mage_Backend_Model_Url::getSecure
     * @magentoConfigFixture admin/routers/adminhtml/args/frontName admin
     * @magentoAppIsolation enabled
     */
    public function testGetUrl()
    {
        $url = $this->_model->getUrl('adminhtml/auth/login');
        $this->assertContains('admin/auth/login/key/', $url);
    }

    /**
     * @param string $controller
     * @param string $action
     * @param string $expectedHash
     * @magentoConfigFixture global/helpers/core/encryption_model Mage_Core_Model_Encryption
     * @dataProvider getSecretKeyDataProvider
     * @magentoAppIsolation enabled
     */
    public function testGetSecretKey($controller, $action, $expectedHash)
    {
        $request = new Mage_Core_Controller_Request_Http;
        $request->setControllerName('default_controller')->setActionName('default_action');
        $this->_model->setRequest($request);
        Mage::getSingleton('Mage_Core_Model_Session')->setData('_form_key', 'salt');
        $this->assertEquals($expectedHash, $this->_model->getSecretKey($controller, $action));
    }

    /**
     * @return array
     */
    public function getSecretKeyDataProvider()
    {
        return array(
            array('', '', 'ae90f9dc052b0f2567b989b38dbfd7f7'),
            array('', 'action', '3cb46d2fac46f6cecd37803a8ea15109'),
            array('controller', '', '8ae895734a8706dec3fbd69fb21e1b77'),
            array('controller', 'action', 'c36d05473b54f437889608cbe8d50339'),
        );
        // md5('controlleractionsalt') .
    }

    /**
     * @magentoConfigFixture global/helpers/core/encryption_model Mage_Core_Model_Encryption
     * @magentoAppIsolation enabled
     */
    public function testGetSecretKeyForwarded()
    {
        $request = new Mage_Core_Controller_Request_Http;
        $request->setControllerName('controller')->setActionName('action');
        $request->initForward()->setControllerName(uniqid())->setActionName(uniqid());
        $this->_model->setRequest($request);
        Mage::getSingleton('Mage_Core_Model_Session')->setData('_form_key', 'salt');
        $this->assertEquals('c36d05473b54f437889608cbe8d50339', $this->_model->getSecretKey());
    }

    public function testUseSecretKey()
    {
        $this->_model->setNoSecret(true);
        $this->assertFalse($this->_model->useSecretKey());

        $this->_model->setNoSecret(false);
        $this->assertTrue($this->_model->useSecretKey());
    }
}
