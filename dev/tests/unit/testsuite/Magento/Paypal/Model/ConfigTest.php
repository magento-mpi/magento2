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
        $this->assertContains(Config::METHOD_WPP_PE_EXPRESS, $this->_model->getCountryMethods('CA'));
        $this->assertNotContains(Config::METHOD_WPP_PE_EXPRESS, $this->_model->getCountryMethods('GB'));
        $this->assertContains(Config::METHOD_WPP_PE_EXPRESS, $this->_model->getCountryMethods('CA'));
        $this->assertContains(Config::METHOD_WPP_EXPRESS, $this->_model->getCountryMethods('DE'));
        $this->assertContains(Config::METHOD_BILLING_AGREEMENT, $this->_model->getCountryMethods('DE'));
    }
}
