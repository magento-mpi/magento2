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
 * Test class for \Magento\ImportExport\Model\Source\Import\Behavior\Basic
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
        \Magento\ImportExport\Model\Import::BEHAVIOR_APPEND,
        \Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE,
        \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE,
    );

    public function setUp()
    {
        parent::setUp();
        $this->_model = new \Magento\ImportExport\Model\Source\Import\Behavior\Basic();
    }

    /**
     * Test toArray method
     *
     * @covers \Magento\ImportExport\Model\Source\Import\Behavior\Basic::toArray
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
     * @covers \Magento\ImportExport\Model\Source\Import\Behavior\Basic::getCode
     */
    public function testGetCode()
    {
        $this->assertEquals($this->_expectedCode, $this->_model->getCode());
    }
}
