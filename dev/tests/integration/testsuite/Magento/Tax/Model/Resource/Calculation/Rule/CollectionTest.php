<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_Resource_Calculation_Rule_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
    }

    /**
     * Test setClassTypeFilter with correct Class Type
     *
     * @param $classType
     * @param $elementId
     * @param $expected
     *
     * @dataProvider setClassTypeFilterDataProvider
     */
    public function testSetClassTypeFilter($classType, $elementId, $expected)
    {
        $collection = $this->_objectManager->create('Magento_Tax_Model_Resource_Calculation_Rule_Collection');
        $collection->setClassTypeFilter($classType, $elementId);
        $this->assertRegExp($expected, (string)$collection->getSelect());
    }

    public function setClassTypeFilterDataProvider()
    {
        return array(
            array(Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT, 1,
                '/`?cd`?\.`?product_tax_class_id`? = [\S]{0,1}1[\S]{0,1}/'),
            array(Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER, 1,
                '/`?cd`?\.`?customer_tax_class_id`? = [\S]{0,1}1[\S]{0,1}/')
        );
    }

    /**
     * Test setClassTypeFilter with wrong Class Type
     *
     * @expectedException Magento_Core_Exception
     */
    public function testSetClassTypeFilterWithWrongType()
    {
        $collection = $this->_objectManager->create('Magento_Tax_Model_Resource_Calculation_Rule_Collection');
        $collection->setClassTypeFilter('WrongType', 1);
    }
}
