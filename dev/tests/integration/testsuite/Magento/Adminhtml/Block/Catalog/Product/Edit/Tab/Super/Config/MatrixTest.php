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
        Mage::register('current_product', Mage::getModel('Magento\Catalog\Model\Product')->load(1));
        Mage::app()->getLayout()->createBlock('Magento\Core\Block\Text', 'head');
        /** @var $usedAttribute \Magento\Catalog\Model\Entity\Attribute */
        $usedAttribute = Mage::getSingleton('Magento\Catalog\Model\Entity\Attribute')->loadByCode(
            Mage::getSingleton('Magento\Eav\Model\Config')->getEntityType('catalog_product')->getId(),
            'test_configurable'
        );
        $attributeOptions = $usedAttribute->getSource()->getAllOptions(false);
        /** @var $block \Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config\Matrix */
        $block = Mage::app()->getLayout()->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config\Matrix');

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
