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
 *
 * @group module:Mage_Backend
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
     * @covers Mage_Backend_Model_Url::getSecure
     * @magentoConfigFixture admin/routers/adminhtml/args/frontName admin
     */
    public function testGetUrl()
    {
        $url = $this->_model->getUrl('adminhtml/auth/login');
        $this->assertContains('admin/auth/login/key/', $url);
    }

    /**
     * @covers Mage_Backend_Model_Url::getSecretKey
     */
    public function testGetSecretKey()
    {
        Mage::getSingleton('Mage_Core_Model_Session')->setFormKey('salt');
        $key = $this->_model->getSecretKey('controller', 'action');
        $this->assertGreaterThan(15, strlen($key));
    }

    /**
     * @covers Mage_Backend_Model_Url::useSecretKey
     */
    public function testUseSecretKey()
    {
        $this->_model->setNoSecret(true);
        $this->assertFalse($this->_model->useSecretKey());

        $this->_model->setNoSecret(false);
        $this->assertTrue($this->_model->useSecretKey());
    }
}
