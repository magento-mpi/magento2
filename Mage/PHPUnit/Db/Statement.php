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
 * Statement for local database adapter
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Db_Statement extends Zend_Db_Statement
{
    /**
     * Result array
     *
     * @var array
     */
    protected $_result = array();

    /**
     * Rows count in result
     *
     * @var int
     */
    protected $_rowCount = 0;

    /**
     * Empty method. We don't need it for this statement
     */
    public function closeCursor()
    {
    }

    /**
     * Empty method. We don't need it for this statement
     */
    public function columnCount()
    {
    }

    /**
     * Empty method. We don't need it for this statement
     */
    public function errorCode()
    {
    }

    /**
     * Empty method. We don't need it for this statement
     */
    public function errorInfo()
    {
    }

    /**
     * Returns next data from result array.
     *
     * @param int|null $style one of Zend_Db::FETCH_* constants
     * @param null $cursor isn't used
     * @param null $offset isn't used
     * @return array of field-value pairs
     */
    public function fetch($style = null, $cursor = null, $offset = null)
    {
        $value = current($this->_result);
        next($this->_result);
        return Mage_PHPUnit_Db_Statement_Fetcher_Factory::getFetcher($style)->fetch($value);
    }

    /**
     * Sets result array from local server (from Mage_PHPUnit_Db_FixtureConnection)
     *
     * @param array $result
     */
    public function setResult($result)
    {
        $this->_result = $result;
        $this->_rowCount = count($result);
        reset($this->_result);
    }

    /**
     * Empty method. We don't need it for this statement
     */
    public function nextRowset()
    {
    }

    /**
     * Returns row count in result
     *
     * @return int
     */
    public function rowCount()
    {
        return $this->_rowCount;
    }

    /**
     * Execute method's stub.
     *
     * @param array $params
     * @return array
     */
    protected function _execute($params)
    {
        return array();
    }
}
