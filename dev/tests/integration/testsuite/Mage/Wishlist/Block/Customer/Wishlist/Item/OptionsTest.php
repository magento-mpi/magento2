<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Wishlist
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Wishlist_Block_Customer_Wishlist_Item_Options.
 */
class Mage_Wishlist_Block_Customer_Wishlist_Item_OptionsTest extends PHPUnit_Framework_TestCase
{
    public function testGetTemplate()
    {
        $block = new Mage_Wishlist_Block_Customer_Wishlist_Item_Options;
        $this->assertEmpty($block->getTemplate());
        $product = new Varien_Object(array('type_id' => 'test'));
        $item = new Varien_Object(array('product' => $product));
        $block->setItem($item);
        $this->assertNotEmpty($block->getTemplate());
        $block->setTemplate('template');
        $this->assertEquals('template', $block->getTemplate());
    }
}
