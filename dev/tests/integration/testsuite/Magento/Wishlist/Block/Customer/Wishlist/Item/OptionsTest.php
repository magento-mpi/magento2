<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Wishlist_Block_Customer_Wishlist_Item_Options.
 */
class Magento_Wishlist_Block_Customer_Wishlist_Item_OptionsTest extends PHPUnit_Framework_TestCase
{
    public function testGetTemplate()
    {
        $block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
            ->createBlock('Magento_Wishlist_Block_Customer_Wishlist_Item_Options');
        $this->assertEmpty($block->getTemplate());
        $product = new Magento_Object(array('type_id' => 'test'));
        $item = new Magento_Object(array('product' => $product));
        $block->setItem($item);
        $this->assertNotEmpty($block->getTemplate());
        $block->setTemplate('template');
        $this->assertEquals('template', $block->getTemplate());
    }
}
