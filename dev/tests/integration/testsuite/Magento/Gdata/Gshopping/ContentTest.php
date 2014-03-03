<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Gdata\Gshopping;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    public function testNewEntry()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Gdata\Gshopping\Content $context */
        $context = $objectManager->create('Magento\Gdata\Gshopping\Content');
        $entry = $context->newEntry();
        $this->assertInstanceOf('Magento\Gdata\Gshopping\Entry', $entry);
        $this->assertEquals($context, $entry->getService());
    }
}
