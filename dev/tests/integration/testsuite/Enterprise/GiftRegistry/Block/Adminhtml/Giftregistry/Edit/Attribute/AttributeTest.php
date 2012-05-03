<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftRegistry
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Attribute_AttributeTest
    extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Core_Model_Layout */
    protected $_layout = null;

    /** @var Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Attribute_Attribute */
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = new Mage_Core_Model_Layout;
        $this->_block = $this->_layout
            ->createBlock('Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Attribute_Attribute');
    }

    public function testGetAddButtonId()
    {
        $expected = 'test_id';
        $addButtonBlock = $this->_layout->addBlock(
            'Mage_Core_Block_Text',
            'add_button',
            $this->_block->getNameInLayout()
        );
        $this->assertEmpty($this->_block->getAddButtonId());
        $addButtonBlock->setId($expected);
        $this->assertEquals($expected, $this->_block->getAddButtonId());
    }
}
