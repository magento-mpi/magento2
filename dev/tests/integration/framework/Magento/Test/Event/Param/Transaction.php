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
 * Parameter holder for transaction events
 */
class Magento_Test_Event_Param_Transaction
{
    /**
     * @var bool
     */
    protected $_isStartRequested;

    /**
     * @var bool
     */
    protected $_isRollbackRequested;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_isStartRequested = false;
        $this->_isRollbackRequested = false;
    }

    /**
     * Request to start transaction
     */
    public function requestTransactionStart()
    {
        $this->_isStartRequested = true;
    }

    /**
     * Request to rollback transaction
     */
    public function requestTransactionRollback()
    {
        $this->_isRollbackRequested = true;
    }

    /**
     * Whether transaction start has been requested or not
     *
     * @return bool
     */
    public function isTransactionStartRequested()
    {
        return $this->_isStartRequested;
    }

    /**
     * Whether transaction rollback has been requested or not
     *
     * @return bool
     */
    public function isTransactionRollbackRequested()
    {
        return $this->_isRollbackRequested;
    }
}
