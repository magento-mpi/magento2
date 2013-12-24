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

use Magento\Outbound\Formatter\Factory as FormatterFactory;
use Magento\Outbound\EndpointInterface;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var FormatterFactory */
    protected $_formatterFactory;

    protected function setUp()
    {
        $this->_formatterFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Outbound\Formatter\Factory', array(
                    'formatterMap' => array(
                        EndpointInterface::FORMAT_JSON => 'Magento\Outbound\Formatter\Json'
                    )
                ));
    }

    public function testGetFormatter()
    {
        $formatter = $this->_formatterFactory->getFormatter(EndpointInterface::FORMAT_JSON);
        $this->assertInstanceOf('Magento\Outbound\Formatter\Json', $formatter);
    }

    public function testGetFormatterIsCached()
    {
        $formatter = $this->_formatterFactory->getFormatter(EndpointInterface::FORMAT_JSON);
        $formatter2 = $this->_formatterFactory->getFormatter(EndpointInterface::FORMAT_JSON);
        $this->assertSame($formatter, $formatter2);
    }
}
