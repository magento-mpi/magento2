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

class Magento_Adminhtml_Block_Catalog_Product_Helper_Form_WeightTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Data_Form_Factory
     */
    protected $_formFactory;

    protected function setUp()
    {
        $this->_objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_formFactory = $this->_objectManager->create('Magento_Data_Form_Factory');
    }

    /**
     * @param string $type
     * @dataProvider virtualTypesDataProvider
     */
    public function testIsVirtualChecked($type)
    {
        /** @var $currentProduct Magento_Catalog_Model_Product */
        $currentProduct = $this->_objectManager->create('Magento_Catalog_Model_Product');
        $currentProduct->setTypeInstance($this->_objectManager->create($type));
        /** @var $block Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Weight */
        $block = $this->_objectManager->create('Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Weight');
        $form = $this->_formFactory->create();
        $form->setDataObject($currentProduct);
        $block->setForm($form);

        $this->assertContains('checked="checked"', $block->getElementHtml(),
            'Is Virtual checkbox is not selected for virtual products');
    }

    /**
     * @return array
     */
    public static function virtualTypesDataProvider()
    {
        return array(
            array('Magento_Catalog_Model_Product_Type_Virtual'),
            array('Magento_Downloadable_Model_Product_Type'),
        );
    }

    /**
     * @param string $type
     * @dataProvider physicalTypesDataProvider
     */
    public function testIsVirtualUnchecked($type)
    {
        /** @var $currentProduct Magento_Catalog_Model_Product */
        $currentProduct = $this->_objectManager->create('Magento_Catalog_Model_Product');
        $currentProduct->setTypeInstance($this->_objectManager->create($type));

        /** @var $block Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Weight */
        $block = $this->_objectManager->create('Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Weight');
        $form = $this->_formFactory->create();
        $form->setDataObject($currentProduct);
        $block->setForm($form);

        $this->assertNotContains('checked="checked"', $block->getElementHtml(),
            'Is Virtual checkbox is selected for physical products');
    }

    /**
     * @return array
     */
    public static function physicalTypesDataProvider()
    {
        return array(
            array('Magento_Catalog_Model_Product_Type_Simple'),
            array('Magento_Bundle_Model_Product_Type'),
        );
    }
}
