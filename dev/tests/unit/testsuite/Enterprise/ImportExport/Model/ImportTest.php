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

/**
 * Test class for Enterprise_ImportExport_Model_Import
 */
class Enterprise_ImportExport_Model_ImportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Enterprise data import model
     *
     * @var Enterprise_ImportExport_Model_Import
     */
    protected $_model;

    /**
     * Init model for future tests
     */
    public function setUp()
    {
        $this->_model = new Enterprise_ImportExport_Model_Import(
            $this->getMock('Magento_ImportExport_Helper_Data', array(), array(), '', false, false)
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
        /** @var $operation Enterprise_ImportExport_Model_Scheduled_Operation */
        $operation = $this->getMock('Enterprise_ImportExport_Model_Scheduled_Operation', null, array(), '', false);
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
