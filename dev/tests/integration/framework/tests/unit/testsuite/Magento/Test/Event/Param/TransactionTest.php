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
 * Test class for Magento_Test_Event_Param_Transaction.
 */
class Magento_Test_Event_Param_TransactionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Event_Param_Transaction
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = new Magento_Test_Event_Param_Transaction();
    }

    public function testConstructor()
    {
        $this->_object->requestTransactionBegin();
        $this->_object->requestTransactionRollback();
        $this->_object->__construct($this);
        $this->assertFalse($this->_object->isTransactionBeginRequested());
        $this->assertFalse($this->_object->isTransactionRollbackRequested());
    }

    public function testRequestTransactionBegin()
    {
        $this->assertFalse($this->_object->isTransactionBeginRequested());
        $this->_object->requestTransactionBegin();
        $this->assertTrue($this->_object->isTransactionBeginRequested());
    }

    public function testRequestTransactionRollback()
    {
        $this->assertFalse($this->_object->isTransactionRollbackRequested());
        $this->_object->requestTransactionRollback();
        $this->assertTrue($this->_object->isTransactionRollbackRequested());
    }
}
