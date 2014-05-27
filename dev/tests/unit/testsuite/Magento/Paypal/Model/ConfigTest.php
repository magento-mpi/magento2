<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $_model;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject('Magento\Paypal\Model\Config');
    }

    public function testGetCountryMethods()
    {
        $this->assertNotContains('payflow_direct', $this->_model->getCountryMethods('GB'));
        $this->assertContains(Config::METHOD_WPP_PE_EXPRESS, $this->_model->getCountryMethods('CA'));
        $this->assertNotContains(Config::METHOD_WPP_PE_EXPRESS, $this->_model->getCountryMethods('GB'));
        $this->assertContains(Config::METHOD_WPP_PE_EXPRESS, $this->_model->getCountryMethods('CA'));
        $this->assertContains(Config::METHOD_WPP_EXPRESS, $this->_model->getCountryMethods('DE'));
        $this->assertContains(Config::METHOD_BILLING_AGREEMENT, $this->_model->getCountryMethods('DE'));
    }

    public function testGetBuildNotationCode()
    {
        $this->_model->setMethod('payflow_direct');
        $this->assertEquals('Magento_Cart_WPP_some-country', $this->_model->getBuildNotationCode('some-country'));
    }

    public function testIsMethodActive()
    {
        $this->assertFalse($this->_model->isMethodActive('payflow_direct'));
    }

    public function testIsMethodAvailable()
    {
        $this->assertFalse($this->_model->isMethodAvailable('payflow_direct'));
    }

    public function testIsCreditCardMethod()
    {
        $this->assertFalse($this->_model->getIsCreditCardMethod('payflow_direct'));
    }

    public function testGetSpecificConfigPath()
    {
        $this->_model->setMethod('payflow_direct');
        $this->assertNull($this->_model->getConfigValue('useccv'));
        $this->assertNull($this->_model->getConfigValue('vendor'));
    }
}
