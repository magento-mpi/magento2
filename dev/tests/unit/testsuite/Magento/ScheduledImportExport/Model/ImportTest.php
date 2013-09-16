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

/**
 * Test class for Magento_ScheduledImportExport_Model_Import
 */
class Magento_ScheduledImportExport_Model_ImportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Enterprise data import model
     *
     * @var Magento_ScheduledImportExport_Model_Import
     */
    protected $_model;

    /**
     * Init model for future tests
     */
    public function setUp()
    {
        $coreConfig = $this->getMock('Magento_Core_Model_Config', array('date'), array(), '', false);
        $config = $this->getMock('Magento_ImportExport_Model_Config', array('date'), array(), '', false);
        
        $this->_model = new Magento_ScheduledImportExport_Model_Import(
            $this->getMock('Magento_ScheduledImportExport_Helper_Data', array(), array(), '', false, false),
            $coreConfig,
            $config
        );
    }

    /**
     * Unset test model
     */
    public function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Test for method 'initialize'
     */
    public function testInitialize()
    {
        $operationData = array(
            'entity_type'    => 'customer',
            'behavior'       => 'update',
            'operation_type' => 'import',
            'start_time'     => '00:00:00',
            'id'             => 1
        );
        /** @var $operation Magento_ScheduledImportExport_Model_Scheduled_Operation */
        $operation = $this->getMock(
            'Magento_ScheduledImportExport_Model_Scheduled_Operation', null, array(), '', false);
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
        $modelDataMap = array(
            'entity_type' => 'entity',
            'start_time'  => 'run_at',
            'id'          => 'scheduled_operation_id'
        );

        if (array_key_exists($key, $modelDataMap)) {
            return $modelDataMap[$key];
        }

        return $key;
    }
}
