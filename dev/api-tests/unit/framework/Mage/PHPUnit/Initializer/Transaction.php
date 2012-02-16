<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
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
