<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_SalesArchive_Model_ArchiveTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_SalesArchive_Model_Archive
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= Mage::getModel('Magento_SalesArchive_Model_Archive');
    }

    /**
     * @param string $entity
     * @dataProvider getEntityResourceModelDataProvider
     */
    public function testGetEntityResourceModel($entity)
    {
        $entityResModel = $this->_model->getEntityResourceModel($entity);
        $this->assertNotEmpty($entityResModel);
        $this->assertTrue(class_exists($entityResModel));
    }

    /**
     * @return array
     */
    public function getEntityResourceModelDataProvider()
    {
        return array(
            array(Magento_SalesArchive_Model_Archive::ORDER),
            array(Magento_SalesArchive_Model_Archive::INVOICE),
            array(Magento_SalesArchive_Model_Archive::SHIPMENT),
            array(Magento_SalesArchive_Model_Archive::CREDITMEMO)
        );
    }

    public function testGetEntityResourceModelForUnknown()
    {
        $entityResModel = $this->_model->getEntityResourceModel('something_wrong');
        $this->assertFalse($entityResModel);
    }

    /**
     * @param mixed $object
     * @param string|false $expectedResult
     * @dataProvider detectArchiveEntityDataProvider
     */
    public function testDetectArchiveEntity($object, $expectedResult)
    {
        $actualResult = $this->_model->detectArchiveEntity(Mage::getModel($object));
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function detectArchiveEntityDataProvider()
    {
        return array(
            array(
                'Magento_Sales_Model_Order',
                Magento_SalesArchive_Model_Archive::ORDER
            ),
            array(
                'Magento_Sales_Model_Resource_Order',
                Magento_SalesArchive_Model_Archive::ORDER
            ),
            array(
                'Magento_Sales_Model_Order_Invoice',
                Magento_SalesArchive_Model_Archive::INVOICE
            ),
            array(
                'Magento_Sales_Model_Resource_Order_Invoice',
                Magento_SalesArchive_Model_Archive::INVOICE
            ),
            array(
                'Magento_Sales_Model_Order_Shipment',
                Magento_SalesArchive_Model_Archive::SHIPMENT
            ),
            array(
                'Magento_Sales_Model_Resource_Order_Shipment',
                Magento_SalesArchive_Model_Archive::SHIPMENT
            ),
            array(
                'Magento_Sales_Model_Order_Creditmemo',
                Magento_SalesArchive_Model_Archive::CREDITMEMO
            ),
            array(
                'Magento_Sales_Model_Resource_Order_Creditmemo',
                Magento_SalesArchive_Model_Archive::CREDITMEMO
            ),
            array('\Magento\Object', false)
        );
    }
}
