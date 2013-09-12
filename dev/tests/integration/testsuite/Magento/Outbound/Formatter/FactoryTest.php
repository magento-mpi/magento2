<?php
/**
 * Magento_Outbound_Formatter_Factory
 *
 * {license_notice}
 *
 * @copyright          {copyright}
 * @license            {license_link}
 */
class Magento_Outbound_Formatter_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Outbound_Formatter_Factory */
    protected $_formatterFactory;

    public function setUp()
    {
        $this->_formatterFactory = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Outbound_Formatter_Factory');
    }

    public function testGetFormatter()
    {
        $formatter = $this->_formatterFactory->getFormatter(Magento_Outbound_EndpointInterface::FORMAT_JSON);
        $this->assertInstanceOf('Magento_Outbound_Formatter_Json', $formatter);
    }

    public function testGetFormatterIsCached()
    {
        $formatter = $this->_formatterFactory->getFormatter(Magento_Outbound_EndpointInterface::FORMAT_JSON);
        $formatter2 = $this->_formatterFactory->getFormatter(Magento_Outbound_EndpointInterface::FORMAT_JSON);
        $this->assertSame($formatter, $formatter2);
    }
}
