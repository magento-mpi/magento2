<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Config;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<root><key>value</key></root>
XML;
        $config = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\App\Config\Base',
            array('sourceData' => $xml)
        );

        $this->assertInstanceOf('Magento\Framework\App\Config\Element', $config->getNode('key'));
    }
}
