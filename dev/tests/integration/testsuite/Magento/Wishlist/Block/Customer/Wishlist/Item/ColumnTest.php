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

class Magento_Wishlist_Block_Customer_Wishlist_Item_ColumnTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout = null;

    /**
     * @var Magento_Wishlist_Block_Customer_Wishlist_Item_Column
     */
    protected $_block = null;

    protected function setUp()
    {
        $this->_layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout');
        $this->_block = $this->_layout->addBlock('Magento_Wishlist_Block_Customer_Wishlist_Item_Column', 'test');
        $this->_layout->addBlock('Magento_Core_Block_Text', 'child', 'test');
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtml()
    {
        $item = new StdClass;
        $this->_block->setItem($item);
        $this->_block->toHtml();
        $this->assertSame($item, $this->_layout->getBlock('child')->getItem());
    }

    public function testGetJs()
    {
        $expected = uniqid();
        $this->_layout->getBlock('child')->setJs($expected);
        $this->assertEquals($expected, $this->_block->getJs());
    }
}
