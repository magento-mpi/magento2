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
     * @var \Magento\SalesArchive\Model\Archive
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= Mage::getModel('\Magento\SalesArchive\Model\Archive');
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
            array(\Magento\SalesArchive\Model\Archive::ORDER),
            array(\Magento\SalesArchive\Model\Archive::INVOICE),
            array(\Magento\SalesArchive\Model\Archive::SHIPMENT),
            array(\Magento\SalesArchive\Model\Archive::CREDITMEMO)
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
                '\Magento\Sales\Model\Order',
                \Magento\SalesArchive\Model\Archive::ORDER
            ),
            array(
                '\Magento\Sales\Model\Resource\Order',
                \Magento\SalesArchive\Model\Archive::ORDER
            ),
            array(
                '\Magento\Sales\Model\Order\Invoice',
                \Magento\SalesArchive\Model\Archive::INVOICE
            ),
            array(
                '\Magento\Sales\Model\Resource\Order\Invoice',
                \Magento\SalesArchive\Model\Archive::INVOICE
            ),
            array(
                '\Magento\Sales\Model\Order\Shipment',
                \Magento\SalesArchive\Model\Archive::SHIPMENT
            ),
            array(
                '\Magento\Sales\Model\Resource\Order\Shipment',
                \Magento\SalesArchive\Model\Archive::SHIPMENT
            ),
            array(
                '\Magento\Sales\Model\Order\Creditmemo',
                \Magento\SalesArchive\Model\Archive::CREDITMEMO
            ),
            array(
                '\Magento\Sales\Model\Resource\Order\Creditmemo',
                \Magento\SalesArchive\Model\Archive::CREDITMEMO
            ),
            array('Magento\Object', false)
        );
    }
}
