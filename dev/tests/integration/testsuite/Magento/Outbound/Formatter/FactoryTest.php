<?php
/**
 * \Magento\Outbound\Formatter\Factory
 *
 * {license_notice}
 *
 * @copyright          {copyright}
 * @license            {license_link}
 */
namespace Magento\Outbound\Formatter;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Outbound\Formatter\Factory */
    protected $_formatterFactory;

    protected function setUp()
    {
        $this->_formatterFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Outbound\Formatter\Factory');
    }

    public function testGetFormatter()
    {
        $formatter = $this->_formatterFactory->getFormatter(\Magento\Outbound\EndpointInterface::FORMAT_JSON);
        $this->assertInstanceOf('Magento\Outbound\Formatter\Json', $formatter);
    }

    public function testGetFormatterIsCached()
    {
        $formatter = $this->_formatterFactory->getFormatter(\Magento\Outbound\EndpointInterface::FORMAT_JSON);
        $formatter2 = $this->_formatterFactory->getFormatter(\Magento\Outbound\EndpointInterface::FORMAT_JSON);
        $this->assertSame($formatter, $formatter2);
    }
}
