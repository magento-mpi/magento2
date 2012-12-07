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
 * Interface for Local DB query processors.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_PHPUnit_Db_Query_Interface
{
    /**
     * Process SQL query and sets result into statement from Local Db Server
     *
     * @param Zend_Db_Statement_Interface $statement
     * @param Mage_PHPUnit_Db_FixtureConnection $connection
     * @param string|Zend_Db_Select $sql
     * @param array $bind
     */
    public function process($statement, $connection, $sql, $bind = array());

    /**
     * Checks if this query processor can process passed SQL query.
     *
     * @param string|Zend_Db_Select $sql
     * @return bool
     */
    public function test($sql);

    /**
     * Parses and sets fixture data with queries information to a container
     *
     * @param SimpleXMLElement $fixture
     */
    public function setFixtureData($fixture);
}
