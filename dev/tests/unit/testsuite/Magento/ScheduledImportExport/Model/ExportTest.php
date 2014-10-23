<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\ScheduledImportExport\Model\Export
 */
namespace Magento\ScheduledImportExport\Model;

class ExportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Enterprise data export model
     *
     * @var \Magento\ScheduledImportExport\Model\Export
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_exportConfigMock;

    /**
     * Date value for tests
     *
     * @var string
     */
    protected $_date = '2012-07-12';

    /**
     * Init model for future tests
     */
    protected function setUp()
    {
        $dateModelMock = $this->getMock(
            'Magento\Framework\Stdlib\DateTime\DateTime',
            array('date'),
            array(),
            '',
            false
        );
        $dateModelMock->expects(
            $this->any()
        )->method(
            'date'
        )->will(
            $this->returnCallback(array($this, 'getDateCallback'))
        );

        $this->_model = new \Magento\ScheduledImportExport\Model\Export(
            $this->getMock('Magento\Framework\Logger', array(), array(), '', false),
            $this->getMock('Magento\Framework\Filesystem', array(), array(), '', false),
            $this->getMock('Magento\Framework\Logger\AdapterFactory', array(), array(), '', false),
            $this->getMock('Magento\ImportExport\Model\Export\ConfigInterface', array(), array(), '', false),
            $this->getMock('Magento\ImportExport\Model\Export\Entity\Factory', array(), array(), '', false),
            $this->getMock('Magento\ImportExport\Model\Export\Adapter\Factory', array(), array(), '', false),
            $dateModelMock,
            array()
        );
    }

    /**
     * Test for method 'initialize'
     */
    public function testInitialize()
    {
        $operationData = array(
            'file_info' => array('file_format' => 'csv'),
            'entity_attributes' => array('export_filter' => 'test', 'skip_attr' => 'test'),
            'entity_type' => 'customer',
            'operation_type' => 'export',
            'start_time' => '00:00:00',
            'id' => 1
        );
        $operation = $this->_getOperationMock($operationData);
        $this->_model->initialize($operation);

        foreach ($operationData as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $this->assertEquals($subValue, $this->_model->getData($this->_getMappedValue($subKey)));
                }
            } else {
                $this->assertEquals($value, $this->_model->getData($this->_getMappedValue($key)));
            }
        }
    }

    /**
     * Test for method 'getScheduledFileName'
     *
     * @param array $data
     * @param string $expectedFilename
     * @dataProvider entityTypeDataProvider
     */
    public function testGetScheduledFileName($data, $expectedFilename)
    {
        $operation = $this->_getOperationMock($data);
        $this->_model->initialize($operation);

        // we should set run date because initialize() resets $operation data
        if (!empty($data['run_date'])) {
            $this->_model->setRunDate($data['run_date']);
        }

        $this->assertEquals($expectedFilename, $this->_model->getScheduledFileName(), 'File name is wrong');
    }

    /**
     * Data provider for test 'testGetScheduledFileName'
     *
     * @return array
     */
    public function entityTypeDataProvider()
    {
        return array(
            'Test file name when entity type provided' => array(
                '$data' => array('entity_type' => 'customer', 'operation_type' => 'export'),
                '$expectedFilename' => $this->_date . '_export_customer'
            ),
            'Test file name when entity subtype provided' => array(
                '$data' => array('entity_type' => 'customer_address', 'operation_type' => 'export'),
                '$expectedFilename' => $this->_date . '_export_customer_address'
            ),
            'Test file name when run date provided' => array(
                '$data' => array('entity_type' => 'customer', 'operation_type' => 'export', 'run_date' => '11-11-11'),
                '$expectedFilename' => '11-11-11_export_customer'
            )
        );
    }

    /**
     * Retrieve data keys which used inside test model
     *
     * @param string $key
     * @return mixed
     */
    protected function _getMappedValue($key)
    {
        $modelDataMap = array('entity_type' => 'entity', 'start_time' => 'run_at', 'id' => 'scheduled_operation_id');

        if (array_key_exists($key, $modelDataMap)) {
            return $modelDataMap[$key];
        }

        return $key;
    }

    /**
     * Retrieve operation mock
     *
     * @param array $operationData
     * @return \Magento\ScheduledImportExport\Model\Scheduled\Operation|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getOperationMock(array $operationData)
    {
        /** @var $operation \Magento\ScheduledImportExport\Model\Scheduled\Operation */
        $operation = $this->getMock(
            'Magento\ScheduledImportExport\Model\Scheduled\Operation',
            array('__wakeup'),
            array(),
            '',
            false
        );
        $operation->setData($operationData);

        return $operation;
    }

    /**
     * Callback to use instead \Magento\Framework\Stdlib\DateTime\DateTime::date()
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
