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

namespace Magento\Catalog\Block\Layer;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/Catalog/_files/filterable_attributes.php
     */
    public function testGetFilters()
    {
        $currentCategory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category');
        $currentCategory->load(3);

        /** @var $layer \Magento\Catalog\Model\Layer */
        $layer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Catalog\Model\Layer');
        $layer->setCurrentCategory($currentCategory);

        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout');
        /** @var $block \Magento\Catalog\Block\Layer\View */
        $block = $layout->createBlock('Magento\Catalog\Block\Layer\View', 'block');

        $filters = $block->getFilters();

        $this->assertInternalType('array', $filters);
        $this->assertGreaterThan(3, count($filters)); // At minimum - category filter + 2 fixture attribute filters

        $found = false;
        foreach ($filters as $filter) {
            if ($filter instanceof \Magento\Catalog\Block\Layer\Filter\Category) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Category filter must be present');

        $attributeCodes = array('filterable_attribute_a', 'filterable_attribute_b');
        foreach ($attributeCodes as $attributeCode) {
            $found = false;
            foreach ($filters as $filter) {
                if (!($filter instanceof \Magento\Catalog\Block\Layer\Filter\Attribute)) {
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
