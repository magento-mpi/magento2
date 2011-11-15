<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_SalesArchive
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_SalesArchive
 */
class Enterprise_SalesArchive_Model_ArchiveTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_SalesArchive_Model_Archive
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= new Enterprise_SalesArchive_Model_Archive;
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
            array(Enterprise_SalesArchive_Model_Archive::ORDER),
            array(Enterprise_SalesArchive_Model_Archive::INVOICE),
            array(Enterprise_SalesArchive_Model_Archive::SHIPMENT),
            array(Enterprise_SalesArchive_Model_Archive::CREDITMEMO)
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
        $actualResult = $this->_model->detectArchiveEntity($object);
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function detectArchiveEntityDataProvider()
    {
        return array(
            array(new Mage_Sales_Model_Order, Enterprise_SalesArchive_Model_Archive::ORDER),
            array(new Mage_Sales_Model_Resource_Order, Enterprise_SalesArchive_Model_Archive::ORDER),
            array(new Mage_Sales_Model_Order_Invoice, Enterprise_SalesArchive_Model_Archive::INVOICE),
            array(new Mage_Sales_Model_Resource_Order_Invoice, Enterprise_SalesArchive_Model_Archive::INVOICE),
            array(new Mage_Sales_Model_Order_Shipment, Enterprise_SalesArchive_Model_Archive::SHIPMENT),
            array(new Mage_Sales_Model_Resource_Order_Shipment, Enterprise_SalesArchive_Model_Archive::SHIPMENT),
            array(new Mage_Sales_Model_Order_Creditmemo, Enterprise_SalesArchive_Model_Archive::CREDITMEMO),
            array(new Mage_Sales_Model_Resource_Order_Creditmemo, Enterprise_SalesArchive_Model_Archive::CREDITMEMO),
            array(new Varien_Object, false)
        );
    }
}
