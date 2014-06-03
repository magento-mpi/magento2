<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Gdata\Gshopping;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    public function testNewEntry()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Framework\Gdata\Gshopping\Content $context */
        $context = $objectManager->create('Magento\Framework\Gdata\Gshopping\Content');
        $entry = $context->newEntry();
        $this->assertInstanceOf('Magento\Framework\Gdata\Gshopping\Entry', $entry);
        $this->assertEquals($context, $entry->getService());
    }
}
