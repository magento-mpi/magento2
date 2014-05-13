<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model;

class ExportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Model object which used for tests
     *
     * @var \Magento\ImportExport\Model\Export
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\ImportExport\Model\Export'
        );
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
        $this->_model->setData(array('entity' => $entity));
        $this->_model->getEntityAttributeCollection();
        $this->assertAttributeInstanceOf(
            $expectedEntityType,
            '_entityAdapter',
            $this->_model,
            'Entity adapter property has wrong type'
        );
    }

    /**
     * @return array
     */
    public function getEntityDataProvider()
    {
        return array(
            'product' => array(
                '$entity' => 'catalog_product',
                '$expectedEntityType' => 'Magento\ImportExport\Model\Export\Entity\Product'
            ),
            'customer main data' => array(
                '$entity' => 'customer',
                '$expectedEntityType' => 'Magento\Customer\Model\ImportExport\Export\Customer'
            ),
            'customer address' => array(
                '$entity' => 'customer_address',
                '$expectedEntityType' => 'Magento\Customer\Model\ImportExport\Export\Address'
            )
        );
    }

    /**
     * Test method '_getEntityAdapter' in case when entity is invalid
     *
     * @expectedException \Magento\Framework\Model\Exception
     * @covers \Magento\ImportExport\Model\Export::_getEntityAdapter
     */
    public function testGetEntityAdapterWithInvalidEntity()
    {
        $this->_model->setData(array('entity' => 'test'));
        $this->_model->getEntityAttributeCollection();
    }
}
