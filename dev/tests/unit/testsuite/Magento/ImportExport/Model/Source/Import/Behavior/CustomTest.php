<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_ImportExport_Model_Source_Import_Behavior_Custom
 */
class Magento_ImportExport_Model_Source_Import_Behavior_CustomTest
    extends Magento_ImportExport_Model_Source_Import_BehaviorTestCaseAbstract
{
    /**
     * Expected behavior group code
     *
     * @var string
     */
    protected $_expectedCode = 'custom';

    /**
     * Expected behaviours
     *
     * @var array
     */
    protected $_expectedBehaviors = array(
        Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE,
        Magento_ImportExport_Model_Import::BEHAVIOR_DELETE,
        Magento_ImportExport_Model_Import::BEHAVIOR_CUSTOM,
    );

    public function setUp()
    {
        parent::setUp();
        $this->_model = new Magento_ImportExport_Model_Source_Import_Behavior_Custom(array());
    }

    /**
     * Test toArray method
     *
     * @covers Magento_ImportExport_Model_Source_Import_Behavior_Custom::toArray
     */
    public function testToArray()
    {
        $behaviorData = $this->_model->toArray();
        $this->assertInternalType('array', $behaviorData);
        $this->assertEquals($this->_expectedBehaviors, array_keys($behaviorData));
    }

    /**
     * Test behavior group code
     *
     * @covers Magento_ImportExport_Model_Source_Import_Behavior_Custom::getCode
     */
    public function testGetCode()
    {
        $this->assertEquals($this->_expectedCode, $this->_model->getCode());
    }
}
