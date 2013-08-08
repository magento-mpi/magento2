<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_ImportExport_Model_ExportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Model object which used for tests
     *
     * @var Mage_ImportExport_Model_Export
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Mage_ImportExport_Model_Export');
    }

    /**
     * Test method '_getEntityAdapter' in case when entity is valid
     *
     * @param string $entity
     * @param string $expectedEntityType
     * @dataProvider getEntityDataProvider
     * @covers Mage_ImportExport_Model_Export::_getEntityAdapter
     */
    public function testGetEntityAdapterWithValidEntity($entity, $expectedEntityType)
    {
        $this->_model->setData(array(
            'entity' => $entity
        ));
        $this->_model->getEntityAttributeCollection();
        $this->assertAttributeInstanceOf($expectedEntityType, '_entityAdapter', $this->_model,
            'Entity adapter property has wrong type'
        );
    }

    /**
     * @return array
     */
    public function getEntityDataProvider()
    {
        return array(
            'product'            => array(
                '$entity'             => 'catalog_product',
                '$expectedEntityType' => 'Mage_ImportExport_Model_Export_Entity_Product'
            ),
            'customer main data' => array(
                '$entity'             => 'customer',
                '$expectedEntityType' => 'Mage_ImportExport_Model_Export_Entity_Eav_Customer'
            ),
            'customer address'   => array(
                '$entity'             => 'customer_address',
                '$expectedEntityType' => 'Mage_ImportExport_Model_Export_Entity_Eav_Customer_Address'
            )
        );
    }

    /**
     * Test method '_getEntityAdapter' in case when entity is invalid
     *
     * @expectedException Magento_Core_Exception
     * @covers Mage_ImportExport_Model_Export::_getEntityAdapter
     */
    public function testGetEntityAdapterWithInvalidEntity()
    {
        $this->_model->setData(array(
            'entity' => 'test'
        ));
        $this->_model->getEntityAttributeCollection();
    }
}
