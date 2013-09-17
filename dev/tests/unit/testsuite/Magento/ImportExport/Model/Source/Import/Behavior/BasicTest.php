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
 * Test class for Magento_ImportExport_Model_Source_Import_Behavior_Basic
 */
class Magento_ImportExport_Model_Source_Import_Behavior_BasicTest
    extends Magento_ImportExport_Model_Source_Import_BehaviorTestCaseAbstract
{
    /**
     * Expected behavior group code
     *
     * @var string
     */
    protected $_expectedCode = 'basic';

    /**
     * Expected behaviours
     *
     * @var array
     */
    protected $_expectedBehaviors = array(
        Magento_ImportExport_Model_Import::BEHAVIOR_APPEND,
        Magento_ImportExport_Model_Import::BEHAVIOR_REPLACE,
        Magento_ImportExport_Model_Import::BEHAVIOR_DELETE,
    );

    protected function setUp()
    {
        parent::setUp();
        $this->_model = new Magento_ImportExport_Model_Source_Import_Behavior_Basic();
    }

    /**
     * Test toArray method
     *
     * @covers Magento_ImportExport_Model_Source_Import_Behavior_Basic::toArray
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
     * @covers Magento_ImportExport_Model_Source_Import_Behavior_Basic::getCode
     */
    public function testGetCode()
    {
        $this->assertEquals($this->_expectedCode, $this->_model->getCode());
    }
}
