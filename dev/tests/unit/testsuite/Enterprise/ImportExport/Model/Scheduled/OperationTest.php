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
     * Default date value
     *
     * @var string
     */
    protected $_date = '00-00-00';

    /**
     * Test getHistoryFilePath() method
     *
     * @dataProvider getHistoryFilePathDataProvider
     */
    public function testGetHistoryFilePath($fileInfo, $lastRunDate, $expectedPath)
    {
        $model = $this->_getScheduledOperationModel($fileInfo);

        $model->setLastRunDate($lastRunDate);

        $this->assertEquals($expectedPath, $model->getHistoryFilePath());
    }

    /**
     * Data provider for testGetHistoryFilePath()
     *
     * @return array
     */
    public function getHistoryFilePathDataProvider()
    {
        return array(
            'empty file name' => array(
                '$fileInfo'     => array('file_format' => 'csv'),
                '$lastRunDate'  => null,
                '$expectedPath' => 'dir/' . $this->_date . '_export_catalog_product.csv'
            ),
            'filled file name' => array(
                '$fileInfo'     => array('file_name' => 'test.xls'),
                '$lastRunDate'  => null,
                '$expectedPath' => 'dir/' . $this->_date . '_export_catalog_product.xls'
            ),
            'set last run date' => array(
                '$fileInfo'     => array('file_name' => 'test.xls'),
                '$lastRunDate'  => '11-11-11',
                '$expectedPath' => 'dir/11-11-11_export_catalog_product.xls'
            )
        );
    }

    /**
     * Get mocked model
     *
     * @param array $fileInfo
     * @return Enterprise_ImportExport_Model_Scheduled_Operation|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getScheduledOperationModel(array $fileInfo)
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        $dateModelMock = $this->getMock('Magento_Core_Model_Date', array('date'), array(), '', false);
        $dateModelMock->expects($this->any())
            ->method('date')
            ->will($this->returnCallback(array($this, 'getDateCallback')));

        //TODO Get rid of mocking methods from testing model when this model will be re-factored

        $arguments = $objectManagerHelper->getConstructArguments('Enterprise_ImportExport_Model_Scheduled_Operation');
        $arguments['dateModel'] = $dateModelMock;
        $model = $this->getMock(
            'Enterprise_ImportExport_Model_Scheduled_Operation',
            array(
                'getOperationType',
                'getEntityType',
                '_getHistoryDirPath',
                'getFileInfo',
                '_init'
            ),
            $arguments
        );

        $model->expects($this->once())
            ->method('getOperationType')
            ->will($this->returnValue('export'));
        $model->expects($this->once())
            ->method('getEntityType')
            ->will($this->returnValue('catalog_product'));
        $model->expects($this->once())
            ->method('_getHistoryDirPath')
            ->will($this->returnValue('dir/'));
        $model->expects($this->once())
            ->method('getFileInfo')
            ->will($this->returnValue($fileInfo));

        return $model;
    }

    /**
     * Callback to use instead of Magento_Core_Model_Date::date()
     *
     * @param string $format
     * @param int|string $input
     * @return string
     */
    public function getDateCallback($format, $input = null)
    {
        if (!empty($format) && !is_null($input)) {
            return $input;
        }

        return $this->_date;
    }
}
