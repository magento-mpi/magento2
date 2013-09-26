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
 * Database transaction events manager
 */
class Magento_TestFramework_Event_Transaction
{
    /**
     * @var Magento_TestFramework_EventManager
     */
    protected $_eventManager;

    /**
     * @var Magento_TestFramework_Event_Param_Transaction
     */
    protected $_eventParam;

    /**
     * @var bool
     */
    protected $_isTransactionActive = false;

    /**
     * Constructor
     *
     * @param Magento_TestFramework_EventManager $eventManager
     */
    public function __construct(Magento_TestFramework_EventManager $eventManager)
    {
        $this->_eventManager = $eventManager;
    }

    /**
     * Handler for 'startTest' event
     *
     * @param PHPUnit_Framework_TestCase $test
     */
    public function startTest(PHPUnit_Framework_TestCase $test)
    {
        $this->_processTransactionRequests('startTest', $test);
    }

    /**
     * Handler for 'endTest' event
     *
     * @param PHPUnit_Framework_TestCase $test
     */
    public function endTest(PHPUnit_Framework_TestCase $test)
    {
        $this->_processTransactionRequests('endTest', $test);
    }

    /**
     * Handler for 'endTestSuite' event
     */
    public function endTestSuite()
    {
        $this->_rollbackTransaction();
    }

    /**
     * Query whether there are any requests for transaction operations and performs them
     *
     * @param string $eventName
     * @param PHPUnit_Framework_TestCase $test
     */
    protected function _processTransactionRequests($eventName, PHPUnit_Framework_TestCase $test)
    {
        $param = $this->_getEventParam();
        $this->_eventManager->fireEvent($eventName . 'TransactionRequest', array($test, $param));
        if ($param->isTransactionRollbackRequested()) {
            $this->_rollbackTransaction();
        }
        if ($param->isTransactionStartRequested()) {
            $this->_startTransaction($test);
        }
    }

    /**
     * Start transaction and fire 'startTransaction' event
     *
     * @param PHPUnit_Framework_TestCase $test
     */
    protected function _startTransaction(PHPUnit_Framework_TestCase $test)
    {
        if (!$this->_isTransactionActive) {
            $this->_getAdapter()->beginTransparentTransaction();
            $this->_isTransactionActive = true;
            $this->_eventManager->fireEvent('startTransaction', array($test));
        }
    }

    /**
     * Rollback transaction and fire 'rollbackTransaction' event
     */
    protected function _rollbackTransaction()
    {
        if ($this->_isTransactionActive) {
            $this->_getAdapter()->rollbackTransparentTransaction();
            $this->_isTransactionActive = false;
            $this->_eventManager->fireEvent('rollbackTransaction');
        }
    }

    /**
     * Retrieve database adapter instance
     *
     * @param string $connectionName 'read' or 'write'
     * @return Magento_DB_Adapter_Interface|Magento_TestFramework_Db_Adapter_TransactionInterface
     * @throws Magento_Exception
     */
    protected function _getAdapter($connectionName = 'write')
    {
        /** @var $resource Magento_Core_Model_Resource */
        $resource = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Resource');
        return $resource->getConnection($connectionName);
    }

    /**
     * Retrieve clean instance of transaction event parameter
     *
     * @return Magento_TestFramework_Event_Param_Transaction
     */
    protected function _getEventParam()
    {
        /* reset object state instead of instantiating new object over and over again */
        if (!$this->_eventParam) {
            $this->_eventParam = new Magento_TestFramework_Event_Param_Transaction();
        } else {
            $this->_eventParam->__construct();
        }
        return $this->_eventParam;
    }
}
