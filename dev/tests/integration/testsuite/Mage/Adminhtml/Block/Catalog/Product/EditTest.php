<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Adminhtml_Block_Catalog_Product_Edit
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getMock('Mage_Catalog_Model_Product', array('getAttributes'), array(), '', false);
        $product->expects($this->any())->method('getAttributes')->will($this->returnValue(array()));
        $product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE);
        Mage::register('current_product', $product);
        $this->_block = Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit');
    }

    public function testGetTypeSwitcherData()
    {
        $data = json_decode($this->_block->getTypeSwitcherData(), true);
        $this->assertEquals('simple', $data['current_type']);
        $this->assertEquals(array(), $data['attributes']);
    }
}
