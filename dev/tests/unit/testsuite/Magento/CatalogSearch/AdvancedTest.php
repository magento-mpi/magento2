<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\CatalogSearch\Model;

class AdvancedTest extends \PHPUnit_Framework_TestCase
{
    public function testAddFiltersVerifyAddConditionsToRegistry()
    {
        $registry = new \Magento\Framework\Registry();

        $attributeBackend = $this->getMock(
            'Magento\Catalog\Model\Product\Attribute\Backend\Sku',
            array('getTable'),
            array(),
            '',
            false
        );
        $attributeBackend->expects($this->once())->method('getTable')->will(
            $this->returnValue('catalog_product_entity')
        );

        $productCollection = $this->getMock(
            'Magento\CatalogSearch\Model\Resource\Advanced\Collection',
            array(
                'addAttributeToSelect',
                'setStore',
                'addMinimalPrice',
                'addTaxPercents',
                'addStoreFilter',
                'setVisibility',
                'addFieldsToFilter'
            ),
            array(),
            '',
            false
        );
        $productCollection->expects($this->any())->method('addAttributeToSelect')
            ->will($this->returnValue($productCollection));
        $productCollection->expects($this->any())->method('setStore')
            ->will($this->returnValue($productCollection));
        $productCollection->expects($this->any())->method('addMinimalPrice')
            ->will($this->returnValue($productCollection));
        $productCollection->expects($this->any())->method('addTaxPercents')
            ->will($this->returnValue($productCollection));
        $productCollection->expects($this->any())->method('addStoreFilter')
            ->will($this->returnValue($productCollection));
        $productCollection->expects($this->any())->method('setVisibility')
            ->will($this->returnValue($productCollection));

        $resource = $this->getMock(
            'Magento\CatalogSearch\Model\Resource\Advanced',
            array('prepareCondition', '__wakeup', 'getIdFieldName'),
            array(),
            '',
            false
        );
        $resource->expects($this->any())->method('prepareCondition')->will(
            $this->returnValue(array('like' => '%simple%'))
        );
        $resource->expects($this->any())->method('getIdFieldName')->will($this->returnValue('entity_id'));

        $engine = $this->getMock(
            'Magento\CatalogSearch\Model\Resource\Fulltext\Engine',
            array('getResource', '__wakeup', 'getAdvancedResultCollection'),
            array(),
            '',
            false
        );
        $engine->expects($this->any())->method('getResource')->will($this->returnValue($resource));
        $engine->expects($this->any())->method('getAdvancedResultCollection')->will(
            $this->returnValue($productCollection)
        );

        $engineProvider = $this->getMock(
            'Magento\CatalogSearch\Model\Resource\EngineProvider',
            array('get'),
            array(),
            '',
            false
        );
        $engineProvider->expects($this->any())->method('get')->will($this->returnValue($engine));

        $values = array('sku' => 'simple');

        $attribute = $this->getMock(
            'Magento\Catalog\Model\Resource\Eav\Attribute',
            array('getAttributeCode', 'getStoreLabel', 'getFrontendInput', 'getBackend', 'getBackendType', '__wakeup'),
            array(),
            '',
            false
        );
        $attribute->expects($this->any())->method('getAttributeCode')->will($this->returnValue('sku'));
        $attribute->expects($this->any())->method('getStoreLabel')->will($this->returnValue('SKU'));
        $attribute->expects($this->any())->method('getFrontendInput')->will($this->returnValue('text'));
        $attribute->expects($this->any())->method('getBackend')->will($this->returnValue($attributeBackend));
        $attribute->expects($this->any())->method('getBackendType')->will($this->returnValue('static'));

        $attributeCollection = $this->getMock(
            'Magento\Framework\Data\Collection',
            array('getIterator'),
            array(),
            '',
            false
        );
        $attributeCollection->expects($this->any())->method('getIterator')->will(
            $this->returnValue(new \ArrayIterator(array($attribute)))
        );

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        /** @var \Magento\CatalogSearch\Model\Advanced $instance */
        $instance = $objectManager->getObject(
            'Magento\CatalogSearch\Model\Advanced',
            array(
                'registry' => $registry,
                'engineProvider' => $engineProvider,
                'data' => array('attributes' => $attributeCollection)
            )
        );
        $instance->addFilters($values);

        $this->assertNotNull($registry->registry('advanced_search_conditions'));
    }
}
