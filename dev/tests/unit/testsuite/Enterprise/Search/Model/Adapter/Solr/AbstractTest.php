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

class Enterprise_Search_Model_Adapter_Solr_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Search_Model_Adapter_Solr_Abstract
     */
    protected $_model;

    /**
     * @var Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected $_attribute;

    protected function setUp()
    {
        $this->_model = $this->getMockForAbstractClass('Enterprise_Search_Model_Adapter_Solr_Abstract');

        $attributeClass = 'Mage_Catalog_Model_Resource_Eav_Attribute';
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments(
            Magento_Test_Helper_ObjectManager::MODEL_ENTITY,
            $attributeClass
        );
        $this->_attribute = $this->getMock($attributeClass, array('_init'), $arguments);
    }

    /**
     * Check Sku processing by getSearchEngineFieldName method with default target
     */
    public function testGetSearchEngineFieldNameSkuTargetDefault()
    {
        $this->_attribute->setAttributeCode('sku');
        $fieldName = $this->_model->getSearchEngineFieldName($this->_attribute);
        $this->assertEquals($fieldName, 'sku');
    }

    /**
     * Check Sku processing by getSearchEngineFieldName method with sort target
     */
    public function testGetSearchEngineFieldNameSkuTargetSort()
    {
        $this->_attribute->setAttributeCode('sku');
        $fieldName = $this->_model->getSearchEngineFieldName($this->_attribute, 'sort');
        $this->assertEquals($fieldName, 'attr_sort_sku');
    }
}
