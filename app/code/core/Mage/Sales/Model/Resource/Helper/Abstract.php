<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Mysql resource helper model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Sales_Model_Resource_Helper_Abstract extends Mage_Core_Model_Resource_Helper_Abstract
{
    /**
     * Main table name
     * 
     * @var string
     */
    protected $_mainTableName = '';

    /**
     * Aggregation aliases
     * 
     * @var array
     */
    protected $_aggregationAliases = array();

    /**
     * Set main table name
     * 
     * @param string $tableName
     */
    public function setMainTableName($tableName)
    {
        $this->_mainTableName = $tableName;
        return $this;
    }

    /**
     * Init aliases for aggregation
     * 
     * @param array $aliases
     * @return Mage_Sales_Model_Resource_Helper_Mysql4
     */
    public function setAggregationAliases($aliases)
    {
        $this->_aggregationAliases = $aliases;
        return $this;
    }

    /**
     * Update rating position
     *
     * @param string $aggregationTableName
     * @return Mage_Sales_Model_Resource_Report_Bestsellers
     */
    abstract public function getBestsellersReportUpdateRatingPos($aggregation, $aggregationTable);
}
