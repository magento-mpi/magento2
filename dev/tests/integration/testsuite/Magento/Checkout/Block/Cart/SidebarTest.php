<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Checkout_Block_Cart_SidebarTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Checkout_Block_Cart_Sidebar
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
            ->createBlock('Magento_Checkout_Block_Cart_Sidebar');
    }

    public function testGetCacheKeyInfo()
    {
        $this->assertEquals(array(
            'BLOCK_TPL',
            'default',
            $this->_block->getTemplateFile(),
            'template' => null,
            'item_renders' => 'default|Magento_Checkout_Block_Cart_Item_Renderer|cart/item/default.phtml',
        ), $this->_block->getCacheKeyInfo());
    }
}
