<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_ImportExport
 * @subpackage  unit_tests
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
        $this->_model = $this->getMock(
            'Enterprise_ImportExport_Model_Scheduled_Operation',
            array('getOperationType', 'getEntityType', 'getFileInfo', '_getCurrentTime', '_getHistoryDirPath', '_construct')
        );
        $this->_model->expects($this->once())
            ->method('getOperationType')
            ->will($this->returnValue('export'));
        $this->_model->expects($this->once())
            ->method('getEntityType')
            ->will($this->returnValue('catalog_product'));
        $this->_model->expects($this->once())
            ->method('_getCurrentTime')
            ->will($this->returnValue('00-00-00'));
        $this->_model->expects($this->once())
            ->method('_getHistoryDirPath')
            ->will($this->returnValue('dir/'));
    }

    /**
     * Tear down before test
     */
    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Test getHistoryFilePath() method in case when file name is not provided
     */
    public function testGetHistoryFilePathWithoutFileName()
    {
        $fileInfo = array('file_format' => 'csv');

        $this->_model->expects($this->once())
            ->method('getFileInfo')
            ->will($this->returnValue($fileInfo));

        $this->assertEquals('dir/00-00-00_export_catalog_product.csv', $this->_model->getHistoryFilePath());
    }

    /**
     * Test getHistoryFilePath() method in case when file name is provided
     */
    public function testGetHistoryFilePathWithFileName()
    {
        $fileInfo = array('file_name' => 'test.xls');

        $this->_model->expects($this->once())
            ->method('getFileInfo')
            ->will($this->returnValue($fileInfo));

        $this->assertEquals('dir/00-00-00_export_catalog_product.xls', $this->_model->getHistoryFilePath());
    }
}
