<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model\Catalog\Layer\Filter;

class AttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string|array $givenValue
     * @param string|array $expectedValue
     * @dataProvider getAttributeValues
     */
    public function testApplyFilterToCollectionSelectString($givenValue, $expectedValue)
    {
        $this->markTestIncomplete('MAGETWO-7903');
        $options = array();
        foreach ($this->getAttributeValues() as $testValues) {
            $options[] = array(
                'label'=> $testValues[0],
                'value'=> $testValues[0]
            );
        }

        $source = $this->getMock('Magento\Eav\Model\Entity\Attribute\Source\Config', array(), array(),
            '', false, false);
        $source->expects($this->any())
            ->method('getAllOptions')
            ->will($this->returnValue($options));
        $attribute = $this->getMock('Magento\Catalog\Model\Resource\Eav\Attribute', array(), array(), '', false, false);
        $attribute->expects($this->any())
            ->method('getSource')
            ->will($this->returnValue($source));

        $productCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Search\Model\Resource\Collection');
        $layer = $this->getMock('Magento\Search\Model\Catalog\Layer');
        $layer->expects($this->any())
            ->method('getProductCollection')
            ->will($this->returnValue($productCollection));

        /**
         * @var \Magento\Search\Model\Layer\Category\Filter\Attribute
         */
        $selectModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Search\Model\Catalog\Layer\Filter\Attribute');
        $selectModel->setAttributeModel($attribute)->setLayer($layer);

        $selectModel->applyFilterToCollection($selectModel, $givenValue);
        $filterParams = $selectModel->getLayer()->getProductCollection()->getExtendedSearchParams();
        $fieldName = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Search\Model\Resource\Engine')
            ->getSearchEngineFieldName($selectModel->getAttributeModel(), 'nav');
        $resultFilter = $filterParams[$fieldName];

        $this->assertContains($expectedValue, $resultFilter);
    }

    public function getAttributeValues()
    {
        return array(
            array('1', '1'),
            array('simple', 'simple'),
            array('0attribute', '0attribute'),
            array(32, 32),
        );
    }
}
