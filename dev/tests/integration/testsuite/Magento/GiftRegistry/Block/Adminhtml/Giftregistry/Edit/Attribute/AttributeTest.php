<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Attribute_AttributeTest
    extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Core_Model_Layout */
    protected $_layout = null;

    /** @var Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Attribute_Attribute */
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = Mage::getSingleton('Magento_Core_Model_Layout');
        $this->_block = $this->_layout
            ->createBlock('Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Attribute_Attribute');
    }

    public function testGetAddButtonId()
    {
        $block = $this->_block->getChildBlock('add_button');
        $expected = uniqid();
        $this->assertNotEquals($expected, $this->_block->getAddButtonId());
        $block->setId($expected);
        $this->assertEquals($expected, $this->_block->getAddButtonId());
    }
}
