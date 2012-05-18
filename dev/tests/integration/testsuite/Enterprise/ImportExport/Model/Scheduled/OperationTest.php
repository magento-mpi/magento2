<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_ImportExport_Model_Scheduled_OperationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_ImportExport_Model_Scheduled_Operation
     */
    protected $_model;

    /**
     * Set up before test
     */
    protected function setUp()
    {
        $this->_model= new Enterprise_ImportExport_Model_Scheduled_Operation();
    }

    /**
     * Tear down before test
     */
    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Get possible operation types
     *
     * @return array
     */
    public function getOperationTypesDataProvider()
    {
        return array(
            'import' => array('$operationType' => 'import'),
            'export' => array('$operationType' => 'export')
        );
    }

    /**
     * Test getInstance() method
     *
     * @dataProvider getOperationTypesDataProvider
     * @param $operationType
     */
    public function testGetInstance($operationType)
    {
        $this->_model->setOperationType($operationType);

        $this->assertInstanceOf(
            'Enterprise_ImportExport_Model_' . uc_words($operationType),
            $this->_model->getInstance()
        );
    }

    /**
     * Test getHistoryFilePath() method in case when file name is not provided
     */
    public function testGetHistoryFilePathWithoutFileName()
    {
        $fileInfo = array('file_format' => 'csv');

        /** @var $operation Enterprise_ImportExport_Model_Scheduled_Operation */
        $operation = $this->getMock(
            'Enterprise_ImportExport_Model_Scheduled_Operation',
            array('getOperationType', 'getEntityType', 'getFileInfo', '_getCurrentTime', '_getHistoryDirPath')
        );
        $operation->expects($this->once())
            ->method('getOperationType')
            ->will($this->returnValue('export'));
        $operation->expects($this->once())
            ->method('getEntityType')
            ->will($this->returnValue('catalog_product'));
        $operation->expects($this->once())
            ->method('_getCurrentTime')
            ->will($this->returnValue('00-00-00'));
        $operation->expects($this->once())
            ->method('_getHistoryDirPath')
            ->will($this->returnValue('dir/'));
        $operation->expects($this->once())
            ->method('getFileInfo')
            ->will($this->returnValue($fileInfo));

        $this->assertEquals('dir/00-00-00_export_catalog_product.csv', $operation->getHistoryFilePath());
    }

    /**
     * Test getHistoryFilePath() method in case when file name is provided
     */
    public function testGetHistoryFilePathWithFileName()
    {
        $fileInfo = array('file_name' => 'test.xls');

        /** @var $operation Enterprise_ImportExport_Model_Scheduled_Operation */
        $operation = $this->getMock(
            'Enterprise_ImportExport_Model_Scheduled_Operation',
            array('getOperationType', 'getEntityType', 'getFileInfo', '_getCurrentTime', '_getHistoryDirPath')
        );
        $operation->expects($this->once())
            ->method('getOperationType')
            ->will($this->returnValue('export'));
        $operation->expects($this->once())
            ->method('getEntityType')
            ->will($this->returnValue('catalog_product'));
        $operation->expects($this->once())
            ->method('_getCurrentTime')
            ->will($this->returnValue('00-00-00'));
        $operation->expects($this->once())
            ->method('_getHistoryDirPath')
            ->will($this->returnValue('dir/'));
        $operation->expects($this->once())
            ->method('getFileInfo')
            ->will($this->returnValue($fileInfo));

        $this->assertEquals('dir/00-00-00_export_catalog_product.xls', $operation->getHistoryFilePath());
    }

    /**
     * Test getHistoryFilePath() method in case when file info is not set
     *
     * @expectedException Mage_Core_Exception
     */
    public function testGetHistoryFilePathException()
    {
        /** @var $operation Enterprise_ImportExport_Model_Scheduled_Operation */
        $operation = $this->getMock(
            'Enterprise_ImportExport_Model_Scheduled_Operation',
            array('getOperationType', 'getEntityType', 'getFileInfo')
        );
        $operation->expects($this->once())
            ->method('getOperationType')
            ->will($this->returnValue('export'));
        $operation->expects($this->once())
            ->method('getEntityType')
            ->will($this->returnValue('catalog_product'));
        $operation->expects($this->once())
            ->method('getFileInfo')
            ->will($this->returnValue(array()));

        $operation->getHistoryFilePath();
    }
}
