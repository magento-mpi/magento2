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
class Magento_Adminhtml_Block_Catalog_Product_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Adminhtml_Block_Catalog_Product_Edit
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        /** @var $product Magento_Catalog_Model_Product */
        $product = $this->getMock('Magento_Catalog_Model_Product', array('getAttributes'), array(), '', false);
        $product->expects($this->any())->method('getAttributes')->will($this->returnValue(array()));
        $product->setTypeId(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE);
        /** @var $objectManager Magento_Test_ObjectManager */
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->register('current_product', $product);
        $this->_block = Mage::app()->getLayout()->createBlock('Magento_Adminhtml_Block_Catalog_Product_Edit');
    }

    public function testGetTypeSwitcherData()
    {
        $data = json_decode($this->_block->getTypeSwitcherData(), true);
        $this->assertEquals('simple', $data['current_type']);
        $this->assertEquals(array(), $data['attributes']);
    }
}
