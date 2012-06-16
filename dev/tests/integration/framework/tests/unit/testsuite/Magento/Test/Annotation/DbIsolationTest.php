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
 * Test class for Magento_Test_Annotation_DbIsolation.
 *
 * @magentoDbIsolation enabled
 */
class Magento_Test_Annotation_DbIsolationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Annotation_DbIsolation
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = new Magento_Test_Annotation_DbIsolation();
    }

    public function testStartTestTransactionRequestClassAnnotation()
    {
        $eventParam = new Magento_Test_Event_Param_Transaction();
        $this->_object->startTestTransactionRequest($this, $eventParam);
        $this->assertTrue($eventParam->isTransactionStartRequested());

        $eventParam = new Magento_Test_Event_Param_Transaction();
        $this->_object->startTransaction($this);
        $this->_object->startTestTransactionRequest($this, $eventParam);
        $this->assertFalse($eventParam->isTransactionStartRequested());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testStartTestTransactionRequestMethodAnnotation()
    {
        $eventParam = new Magento_Test_Event_Param_Transaction();
        $this->_object->startTestTransactionRequest($this, $eventParam);
        $this->assertTrue($eventParam->isTransactionStartRequested());

        $eventParam = new Magento_Test_Event_Param_Transaction();
        $this->_object->startTransaction($this);
        $this->_object->startTestTransactionRequest($this, $eventParam);
        $this->assertTrue($eventParam->isTransactionStartRequested());
    }

    /**
     * @magentoDbIsolation invalid
     * @expectedException Magento_Exception
     */
    public function testStartTestTransactionRequestInvalidAnnotation()
    {
        $this->_object->startTestTransactionRequest($this, new Magento_Test_Event_Param_Transaction());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDbIsolation disabled
     * @expectedException Magento_Exception
     */
    public function testStartTestTransactionRequestAmbiguousAnnotation()
    {
        $this->_object->startTestTransactionRequest($this, new Magento_Test_Event_Param_Transaction());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testEndTestTransactionRequestMethodAnnotation()
    {
        $eventParam = new Magento_Test_Event_Param_Transaction();
        $this->_object->endTestTransactionRequest($this, $eventParam);
        $this->assertFalse($eventParam->isTransactionStartRequested());
        $this->assertFalse($eventParam->isTransactionRollbackRequested());

        $eventParam = new Magento_Test_Event_Param_Transaction();
        $this->_object->startTransaction($this);
        $this->_object->endTestTransactionRequest($this, $eventParam);
        $this->assertFalse($eventParam->isTransactionStartRequested());
        $this->assertTrue($eventParam->isTransactionRollbackRequested());
    }
}
