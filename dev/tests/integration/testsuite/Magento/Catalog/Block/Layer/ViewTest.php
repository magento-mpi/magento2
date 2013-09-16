<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Block_Layer_ViewTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/Catalog/_files/filterable_attributes.php
     */
    public function testGetFilters()
    {
        $currentCategory = Mage::getModel('Magento_Catalog_Model_Category');
        $currentCategory->load(3);

        /** @var $layer Magento_Catalog_Model_Layer */
        $layer = Mage::getSingleton('Magento_Catalog_Model_Layer');
        $layer->setCurrentCategory($currentCategory);

        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getSingleton('Magento_Core_Model_Layout');
        /** @var $block Magento_Catalog_Block_Layer_View */
        $block = $layout->createBlock('Magento_Catalog_Block_Layer_View', 'block');

        $filters = $block->getFilters();

        $this->assertInternalType('array', $filters);
        $this->assertGreaterThan(3, count($filters)); // At minimum - category filter + 2 fixture attribute filters

        $found = false;
        foreach ($filters as $filter) {
            if ($filter instanceof Magento_Catalog_Block_Layer_Filter_Category) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Category filter must be present');

        $attributeCodes = array('filterable_attribute_a', 'filterable_attribute_b');
        foreach ($attributeCodes as $attributeCode) {
            $found = false;
            foreach ($filters as $filter) {
                if (!($filter instanceof Magento_Catalog_Block_Layer_Filter_Attribute)) {
                    continue;
                }
                if ($attributeCode == $filter->getAttributeModel()->getAttributeCode()) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, "Filter for attribute {$attributeCode} must be present");
        }
    }
}
