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
    protected $_isBeginRequested;

    /**
     * @var bool
     */
    protected $_isRollbackRequested;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_isBeginRequested = false;
        $this->_isRollbackRequested = false;
    }

    /**
     * Request to begin transaction
     */
    public function requestTransactionBegin()
    {
        $this->_isBeginRequested = true;
    }

    /**
     * Request to rollback transaction
     */
    public function requestTransactionRollback()
    {
        $this->_isRollbackRequested = true;
    }

    /**
     * Whether transaction begin has been requested or not
     *
     * @return bool
     */
    public function isTransactionBeginRequested()
    {
        return $this->_isBeginRequested;
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
