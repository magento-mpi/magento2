<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_MatrixTest extends Mage_Backend_Area_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Catalog/_files/product_configurable.php
     */
    public function testGetVariations()
    {
        Mage::register('current_product', Mage::getModel('Mage_Catalog_Model_Product')->load(1));
        Mage::app()->getLayout()->createBlock('Mage_Core_Block_Text', 'head');
        /** @var $usedAttribute Mage_Catalog_Model_Entity_Attribute */
        $usedAttribute = Mage::getSingleton('Mage_Catalog_Model_Entity_Attribute')->loadByCode(
            Mage::getSingleton('Mage_Eav_Model_Config')->getEntityType('catalog_product')->getId(),
            'test_configurable'
        );
        $attributeOptions = $usedAttribute->getSource()->getAllOptions(false);
        /** @var $block Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Matrix */
        $block = Mage::app()->getLayout()->createBlock(preg_replace('/Test$/', '', __CLASS__));

        $variations = $block->getVariations();
        foreach ($variations as &$variation) {
            foreach ($variation as &$row) {
                unset($row['price']);
            }
        }

        $this->assertEquals(
            array(
                array($usedAttribute->getId() => $attributeOptions[0]),
                array($usedAttribute->getId() => $attributeOptions[1]),
            ),
            $variations
        );
    }
}
