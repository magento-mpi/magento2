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
 * Test class for Magento_TestFramework_Event_Param_Transaction.
 */
class Magento_Test_Event_Param_TransactionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Event_Param_Transaction
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = new Magento_TestFramework_Event_Param_Transaction();
    }

    public function testConstructor()
    {
        $this->_object->requestTransactionStart();
        $this->_object->requestTransactionRollback();
        $this->_object->__construct($this);
        $this->assertFalse($this->_object->isTransactionStartRequested());
        $this->assertFalse($this->_object->isTransactionRollbackRequested());
    }

    public function testRequestTransactionStart()
    {
        $this->assertFalse($this->_object->isTransactionStartRequested());
        $this->_object->requestTransactionStart();
        $this->assertTrue($this->_object->isTransactionStartRequested());
    }

    public function testRequestTransactionRollback()
    {
        $this->assertFalse($this->_object->isTransactionRollbackRequested());
        $this->_object->requestTransactionRollback();
        $this->assertTrue($this->_object->isTransactionRollbackRequested());
    }
}
