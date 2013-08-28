<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tag_Block_Product_ResultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Tag_Block_Product_Result
     */
    protected $_block = null;

    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout = null;

    /**
     * @var Magento_Core_Block_Text
     */
    protected $_child = null;

    public static function setUpBeforeClass()
    {
        Mage::register('current_tag', new Magento_Object(array('id' => uniqid())));
    }

    protected function setUp()
    {
        $this->_layout = Mage::getModel('Magento_Core_Model_Layout');
        $this->_layout->addBlock('Magento_Core_Block_Text', 'root');
        $this->_layout->addBlock('Magento_Core_Block_Text', 'head');
        $context = Mage::getObjectManager()->create('Magento_Core_Block_Template_Context',
            array('layout' => $this->_layout)
        );
        $this->_block = $this->_layout->createBlock('Magento_Tag_Block_Product_Result', 'test',
            array('context' => $context)
        );
        $this->_child = Mage::getObjectManager()->create('Magento_Core_Block_Text');
        $this->_layout->addBlock($this->_child, 'search_result_list', 'test');
    }

    public function testSetListOrders()
    {
        $this->assertEmpty($this->_child->getData('available_orders'));
        $this->_block->setListOrders();
        $this->assertNotEmpty($this->_child->getData('available_orders'));
    }

    public function testSetListModes()
    {
        $this->assertEmpty($this->_child->getData('modes'));
        $this->_block->setListModes();
        $this->assertNotEmpty($this->_child->getData('modes'));
    }

    public function testSetListCollection()
    {
        $this->assertEmpty($this->_child->getData('collection'));
        $this->_block->setListCollection();
        $this->assertNotEmpty($this->_child->getData('collection'));
    }
}
