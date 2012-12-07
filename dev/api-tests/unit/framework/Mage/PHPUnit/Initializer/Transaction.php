<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Initializer of Mage::$headersSentThrowsException flag
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Initializer_Transaction extends Mage_PHPUnit_Initializer_Abstract
{
    /**
     * Database connection
     *
     * @var Mage_Core_Model_Resource_Abstract
     */
    protected $_connection;

    /**
     * Runs initialization process.
     */
    public function run()
    {
        $this->getConnection()->beginTransaction();
    }

    /**
     * Rollback all changes after the test is ended (on tearDown)
     */
    public function reset()
    {
        $this->getConnection()->rollBack();
    }

    /**
     * Returns database connection object
     *
     * @return Mage_Core_Model_Resource_Abstract
     */
    public function getConnection()
    {
        if (!$this->_connection) {
            $this->_connection = $this->getDefaultConnection();
        }
        return $this->_connection;
    }

    /**
     * Returns default DB connection
     *
     * @return Mage_Core_Model_Resource_Abstract
     */
    public function getDefaultConnection()
    {
        return Mage_PHPUnit_Config::getInstance()->getDefaultConnection();
    }

    /**
     * Sets database connection
     *
     * @param Mage_Core_Model_Resource_Abstract $connection
     */
    public function setConnection($connection)
    {
        $this->_connection = $connection;
    }
}
