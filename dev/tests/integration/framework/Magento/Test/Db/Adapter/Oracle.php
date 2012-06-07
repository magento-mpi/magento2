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
 * See Magento_Test_Db_TransactionInterface
 */
class Magento_Test_Db_Adapter_Oracle extends Varien_Db_Adapter_Oracle
    implements Magento_Test_Db_Adapter_TransactionInterface
{
    /**
     * @var int
     */
    protected $_transparentLevel = 0;

    /**
     * See Magento_Test_Db_Adapter_TransactionInterface
     *
     * @return Magento_Test_Db_Adapter_Oracle
     */
    public function beginTransparentTransaction()
    {
        $this->_transparentLevel += 1;
        return $this->beginTransaction();
    }

    /**
     * See Magento_Test_Db_Adapter_TransactionInterface
     *
     * @return Magento_Test_Db_Adapter_Oracle
     */
    public function commitTransparentTransaction()
    {
        $this->_transparentLevel -= 1;
        return $this->commit();
    }

    /**
     * See Magento_Test_Db_Adapter_TransactionInterface
     *
     * @return Magento_Test_Db_Adapter_Oracle
     */
    public function rollbackTransparentTransaction()
    {
        $this->_transparentLevel -= 1;
        return $this->rollback();
    }

    /**
     * Adjust transaction level with "transparent" counter
     *
     * @return int
     */
    public function getTransactionLevel()
    {
        return parent::getTransactionLevel() - $this->_transparentLevel;
    }
}
