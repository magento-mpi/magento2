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

class Magento_Search_Model_Catalog_Layer_Filter_AttributeTest extends PHPUnit_Framework_TestCase
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

        $source = $this->getMock('Magento_Eav_Model_Entity_Attribute_Source_Config', array(), array(),
            '', false, false);
        $source->expects($this->any())
            ->method('getAllOptions')
            ->will($this->returnValue($options));
        $attribute = $this->getMock('Magento_Catalog_Model_Resource_Eav_Attribute', array(), array(), '', false, false);
        $attribute->expects($this->any())
            ->method('getSource')
            ->will($this->returnValue($source));

        $productCollection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Search_Model_Resource_Collection');
        $layer = $this->getMock('Magento_Search_Model_Catalog_Layer');
        $layer->expects($this->any())
            ->method('getProductCollection')
            ->will($this->returnValue($productCollection));

        /**
         * @var Magento_Search_Model_Catalog_Layer_Filter_Attribute
         */
        $selectModel = Mage::getModel('Magento_Search_Model_Catalog_Layer_Filter_Attribute');
        $selectModel->setAttributeModel($attribute)->setLayer($layer);

        $selectModel->applyFilterToCollection($selectModel, $givenValue);
        $filterParams = $selectModel->getLayer()->getProductCollection()->getExtendedSearchParams();
        $fieldName = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Search_Model_Resource_Engine')
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
