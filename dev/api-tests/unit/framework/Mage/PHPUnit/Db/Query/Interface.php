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
