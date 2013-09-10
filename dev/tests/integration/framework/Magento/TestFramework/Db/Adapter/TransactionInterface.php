<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * DB adapter transaction interface that allows starting transaction with adjusted level,
 * transparently to the application
 */
interface Magento_TestFramework_Db_Adapter_TransactionInterface
{
    /**
     * Increment "transparent" transaction counter and start real transaction
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function beginTransparentTransaction();

    /**
     * Decrement "transparent" transaction counter and commit real transaction
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function commitTransparentTransaction();

    /**
     * Decrement "transparent" transaction counter and rollback real transaction
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function rollbackTransparentTransaction();
}
