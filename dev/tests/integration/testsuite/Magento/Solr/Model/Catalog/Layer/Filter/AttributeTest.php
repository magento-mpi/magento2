<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Model\Catalog\Layer\Filter;

class AttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string|array $givenValue
     * @param string|array $expectedValue
     * @dataProvider getAttributeValues
     */
    public function testApplyFilterToCollectionSelectString($givenValue, $expectedValue)
    {
        $this->markTestSkipped('Solr module disabled');
        $options = [];
        foreach ($this->getAttributeValues() as $testValues) {
            $options[] = ['label' => $testValues[0], 'value' => $testValues[0]];
        }

        $source = $this->getMock(
            'Magento\Eav\Model\Entity\Attribute\Source\Config',
            [],
            [],
            '',
            false,
            false
        );
        $source->expects($this->any())->method('getAllOptions')->will($this->returnValue($options));
        $attribute = $this->getMock(
            'Magento\Catalog\Model\Resource\Eav\Attribute',
            [],
            [],
            '',
            false,
            false
        );
        $attribute->expects($this->any())->method('getSource')->will($this->returnValue($source));

        $productCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Solr\Model\Resource\Collection'
        );
        $layer = $this->getMock(
            'Magento\Catalog\Model\Layer\Category', ['getProductCollection'], [], '', false
        );
        $layer->expects($this->any())
            ->method('getProductCollection')
            ->will($this->returnValue($productCollection));

        /**
         * @var \Magento\Solr\Model\Layer\Category\Filter\Attribute
         */
        $selectModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('\Magento\Solr\Model\Layer\Category\Filter\Attribute', ['layer' => $layer]);
        $selectModel->setAttributeModel($attribute)->setLayer($layer);

        $selectModel->applyFilterToCollection($selectModel, $givenValue);
        $filterParams = $selectModel->getLayer()->getProductCollection()->getExtendedSearchParams();
        $fieldName = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Solr\Model\Resource\Solr\Engine'
        )->getSearchEngineFieldName(
            $selectModel->getAttributeModel(),
            'nav'
        );
        $resultFilter = $filterParams[$fieldName];

        $this->assertContains($expectedValue, $resultFilter);
    }

    public function getAttributeValues()
    {
        return [['1', '1'], ['simple', 'simple'], ['0attribute', '0attribute'], [32, 32]];
    }
}
