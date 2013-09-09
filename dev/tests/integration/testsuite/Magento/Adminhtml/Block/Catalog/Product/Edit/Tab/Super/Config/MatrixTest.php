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
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_MatrixTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_configurable.php
     */
    public function testGetVariations()
    {
        /** @var $objectManager Magento_Test_ObjectManager */
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')
            ->register('current_product', Mage::getModel('Magento_Catalog_Model_Product')->load(1));
        Mage::app()->getLayout()->createBlock('Magento_Core_Block_Text', 'head');
        /** @var $usedAttribute Magento_Catalog_Model_Entity_Attribute */
        $usedAttribute = Mage::getSingleton('Magento_Catalog_Model_Entity_Attribute')->loadByCode(
            Mage::getSingleton('Magento_Eav_Model_Config')->getEntityType('catalog_product')->getId(),
            'test_configurable'
        );
        $attributeOptions = $usedAttribute->getSource()->getAllOptions(false);
        /** @var $block Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Matrix */
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
