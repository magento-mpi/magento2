<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Wishlist\Block\Customer\Wishlist\Item\Options.
 */
namespace Magento\Wishlist\Block\Customer\Wishlist\Item;

class OptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTemplate()
    {
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        )->createBlock(
            'Magento\Wishlist\Block\Customer\Wishlist\Item\Options'
        );
        $this->assertEmpty($block->getTemplate());
        $product = new \Magento\Framework\Object(array('type_id' => 'test'));
        $item = new \Magento\Framework\Object(array('product' => $product));
        $block->setItem($item);
        $this->assertNotEmpty($block->getTemplate());
        $block->setTemplate('template');
        $this->assertEquals('template', $block->getTemplate());
    }
}
