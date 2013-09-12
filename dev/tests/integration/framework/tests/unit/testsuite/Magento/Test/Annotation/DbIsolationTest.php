<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_TestFramework_Annotation_DbIsolation.
 *
 * @magentoDbIsolation enabled
 */
class Magento_Test_Annotation_DbIsolationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Annotation_DbIsolation
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = new Magento_TestFramework_Annotation_DbIsolation();
    }

    public function testStartTestTransactionRequestClassIsolationEnabled()
    {
        $eventParam = new Magento_TestFramework_Event_Param_Transaction();
        $this->_object->startTestTransactionRequest($this, $eventParam);
        $this->assertTrue($eventParam->isTransactionStartRequested());
        $this->assertFalse($eventParam->isTransactionRollbackRequested());

        $eventParam = new Magento_TestFramework_Event_Param_Transaction();
        $this->_object->startTransaction($this);
        $this->_object->startTestTransactionRequest($this, $eventParam);
        $this->assertFalse($eventParam->isTransactionStartRequested());
        $this->assertFalse($eventParam->isTransactionRollbackRequested());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testStartTestTransactionRequestMethodIsolationEnabled()
    {
        $this->testStartTestTransactionRequestClassIsolationEnabled();
    }

    /**
     * @magentoDbIsolation disabled
     */
    public function testStartTestTransactionRequestMethodIsolationDisabled()
    {
        $eventParam = new Magento_TestFramework_Event_Param_Transaction();
        $this->_object->startTestTransactionRequest($this, $eventParam);
        $this->assertFalse($eventParam->isTransactionStartRequested());
        $this->assertFalse($eventParam->isTransactionRollbackRequested());

        $eventParam = new Magento_TestFramework_Event_Param_Transaction();
        $this->_object->startTransaction($this);
        $this->_object->startTestTransactionRequest($this, $eventParam);
        $this->assertFalse($eventParam->isTransactionStartRequested());
        $this->assertTrue($eventParam->isTransactionRollbackRequested());
    }

    /**
     * @magentoDbIsolation invalid
     * @expectedException Magento_Exception
     */
    public function testStartTestTransactionRequestInvalidAnnotation()
    {
        $this->_object->startTestTransactionRequest($this, new Magento_TestFramework_Event_Param_Transaction());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDbIsolation disabled
     * @expectedException Magento_Exception
     */
    public function testStartTestTransactionRequestAmbiguousAnnotation()
    {
        $this->_object->startTestTransactionRequest($this, new Magento_TestFramework_Event_Param_Transaction());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testEndTestTransactionRequestMethodIsolationEnabled()
    {
        $eventParam = new Magento_TestFramework_Event_Param_Transaction();
        $this->_object->endTestTransactionRequest($this, $eventParam);
        $this->assertFalse($eventParam->isTransactionStartRequested());
        $this->assertFalse($eventParam->isTransactionRollbackRequested());

        $eventParam = new Magento_TestFramework_Event_Param_Transaction();
        $this->_object->startTransaction($this);
        $this->_object->endTestTransactionRequest($this, $eventParam);
        $this->assertFalse($eventParam->isTransactionStartRequested());
        $this->assertTrue($eventParam->isTransactionRollbackRequested());
    }
}
