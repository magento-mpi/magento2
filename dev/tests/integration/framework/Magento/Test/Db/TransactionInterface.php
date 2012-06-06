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
 * "Transparent" DB transaction hack for integration tests
 */
interface Magento_Test_Db_TransactionInterface
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

    /**
     * Adjust transaction level with "transparent" counter
     * @return int
     */
    public function getTransactionLevel();
}
