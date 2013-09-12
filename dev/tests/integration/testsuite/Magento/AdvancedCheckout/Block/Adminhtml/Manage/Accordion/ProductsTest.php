<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_ProductsTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Core_Block_Abstract */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $layout = Mage::getModel('Magento_Core_Model_Layout');
        $this->_block = $layout->createBlock('Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Products');
    }

    public function testPrepareLayout()
    {
        $searchBlock = $this->_block->getChildBlock('search_button');
        $this->assertInstanceOf('Magento_Backend_Block_Widget_Button', $searchBlock);
        $this->assertEquals('checkoutObj.searchProducts()', $searchBlock->getOnclick());
    }
}
