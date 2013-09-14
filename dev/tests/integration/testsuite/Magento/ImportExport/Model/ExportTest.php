<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ImportExport_Model_ExportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Model object which used for tests
     *
     * @var \Magento\ImportExport\Model\Export
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento\ImportExport\Model\Export');
    }

    /**
     * Test method '_getEntityAdapter' in case when entity is valid
     *
     * @param string $entity
     * @param string $expectedEntityType
     * @dataProvider getEntityDataProvider
     * @covers \Magento\ImportExport\Model\Export::_getEntityAdapter
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
                '$expectedEntityType' => 'Magento\ImportExport\Model\Export\Entity\Product'
            ),
            'customer main data' => array(
                '$entity'             => 'customer',
                '$expectedEntityType' => 'Magento\ImportExport\Model\Export\Entity\Eav\Customer'
            ),
            'customer address'   => array(
                '$entity'             => 'customer_address',
                '$expectedEntityType' => 'Magento\ImportExport\Model\Export\Entity\Eav\Customer\Address'
            )
        );
    }

    /**
     * Test method '_getEntityAdapter' in case when entity is invalid
     *
     * @expectedException \Magento\Core\Exception
     * @covers \Magento\ImportExport\Model\Export::_getEntityAdapter
     */
    public function testGetEntityAdapterWithInvalidEntity()
    {
        $this->_model->setData(array(
            'entity' => 'test'
        ));
        $this->_model->getEntityAttributeCollection();
    }
}
