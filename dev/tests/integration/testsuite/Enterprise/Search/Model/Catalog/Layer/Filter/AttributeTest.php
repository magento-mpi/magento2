<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Search
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Search_Model_Catalog_Layer_Filter_AttributeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Search_Model_Catalog_Layer_Filter_Attribute
     */
    protected $_selectModel;

    public function setUp()
    {
        $options = array();
        foreach ($this->getAttributeValues() as $testValues) {
            $options[] = array(
                'label'=> $testValues[0],
                'value'=> $testValues[0]
            );
        }

        /**
         * @var Mage_Catalog_Model_Resource_Eav_Attribute
         */
        $attribute = Mage::getModel('Mage_Catalog_Model_Resource_Eav_Attribute')
            ->setAttributeCode('select_test')
            ->setFrontendInput('select')
            ->setStoreId(0);
        $source = $attribute->getSource();
        $sourceReflection = new ReflectionClass(get_class($source));
        $optionProperty = $sourceReflection->getProperty('_options');
        $optionProperty->setAccessible(true);
        $optionProperty->setValue($source, array(0 => $options));

        /**
         * @var Enterprise_Search_Model_Catalog_Layer
         */
        $layer = Mage::getModel('Enterprise_Search_Model_Catalog_Layer');

        $this->_selectModel = Mage::getModel('Enterprise_Search_Model_Catalog_Layer_Filter_Attribute');
        $this->_selectModel->setLayer($layer)->setAttributeModel($attribute);
    }

    /**
     * @param string|array $givenValue
     * @param string|array $expectedValue
     * @dataProvider getAttributeValues
     */
    public function testApplyFilterToCollectionSelect($givenValue, $expectedValue)
    {
        $this->_selectModel->applyFilterToCollection($this->_selectModel, $givenValue);
        $filterParams = $this->_selectModel->getLayer()->getProductCollection()->getExtendedSearchParams();
        $fieldName = Mage::getResourceSingleton('Enterprise_Search_Model_Resource_Engine')
            ->getSearchEngineFieldName($this->_selectModel->getAttributeModel(), 'nav');
        $resultFilter = $filterParams[$fieldName];

        $this->assertTrue(in_array($expectedValue, $resultFilter));
    }

    public function getAttributeValues()
    {
        return array(
            array('1', '1'),
            array('simple', 'simple'),
            array('0attribute', '0attribute'),
        );
    }
}
