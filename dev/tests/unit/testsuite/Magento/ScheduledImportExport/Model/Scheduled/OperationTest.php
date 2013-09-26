<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Model\Scheduled;

class OperationTest extends \PHPUnit_Framework_TestCase
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
     * @return \Magento\ScheduledImportExport\Model\Scheduled\Operation|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getScheduledOperationModel(array $fileInfo)
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $dateModelMock = $this->getMock('Magento\Core\Model\Date', array('date'), array(), '', false);
        $dateModelMock->expects($this->any())
            ->method('date')
            ->will($this->returnCallback(array($this, 'getDateCallback')));

        //TODO Get rid of mocking methods from testing model when this model will be re-factored

        $operationFactory = $this->getMOck(
            'Magento\ScheduledImportExport\Model\Scheduled\Operation\DataFactory', array(), array(), '', false
        );
        $emailInfoFactory = $this->getMOck('Magento\Core\Model\Email\InfoFactory', array(), array(), '', false);
        $params = array(
            'operationFactory' => $operationFactory,
            'emailInfoFactory' => $emailInfoFactory,
        );
        $arguments = $objectManagerHelper->getConstructArguments(
            'Magento\ScheduledImportExport\Model\Scheduled\Operation', $params
        );
        $arguments['dateModel'] = $dateModelMock;
        $model = $this->getMock(
            'Magento\ScheduledImportExport\Model\Scheduled\Operation',
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
     * Callback to use instead of \Magento\Core\Model\Date::date()
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
