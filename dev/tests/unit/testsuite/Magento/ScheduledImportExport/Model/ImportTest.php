<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\ScheduledImportExport\Model\Import
 */
namespace Magento\ScheduledImportExport\Model;

class ImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Enterprise data import model
     *
     * @var \Magento\ScheduledImportExport\Model\Import
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_importConfigMock;

    /**
     * Init model for future tests
     */
    protected function setUp()
    {
        $this->_importConfigMock = $this->getMock('Magento\ImportExport\Model\Import\ConfigInterface');
        $logger = $this->getMock('Magento\Framework\Logger', array(), array(), '', false);
        $indexerFactory = $this->getMock('Magento\Indexer\Model\IndexerFactory', array(), array(), '', false);
        $this->_model = new \Magento\ScheduledImportExport\Model\Import(
            $logger,
            $this->getMock('Magento\Framework\Filesystem', array(), array(), '', false),
            $this->getMock('Magento\Framework\Logger\AdapterFactory', array(), array(), '', false),
            $this->getMock('Magento\ImportExport\Helper\Data', array(), array(), '', false),
            $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface'),
            $this->_importConfigMock,
            $this->getMock('Magento\ImportExport\Model\Import\Entity\Factory', array(), array(), '', false),
            $this->getMock('Magento\ImportExport\Model\Resource\Import\Data', array(), array(), '', false),
            $this->getMock('Magento\ImportExport\Model\Export\Adapter\CsvFactory', array(), array(), '', false),
            $this->getMock('\Magento\Framework\HTTP\Adapter\FileTransferFactory', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\File\UploaderFactory', array(), array(), '', false),
            $this->getMock('Magento\ImportExport\Model\Source\Import\Behavior\Factory', array(), array(), '', false),
            $indexerFactory
        );
    }

    /**
     * Unset test model
     */
    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Test for method 'initialize'
     */
    public function testInitialize()
    {
        $operationData = array(
            'entity_type' => 'customer',
            'behavior' => 'update',
            'operation_type' => 'import',
            'start_time' => '00:00:00',
            'id' => 1
        );
        /** @var $operation \Magento\ScheduledImportExport\Model\Scheduled\Operation */
        $operation = $this->getMock(
            'Magento\ScheduledImportExport\Model\Scheduled\Operation',
            array('__wakeup'),
            array(),
            '',
            false
        );
        $operation->setData($operationData);
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
}
