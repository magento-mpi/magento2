<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Enterprise CustomerSegment Resource Helper Mssql
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Model_Resource_Helper_Mssql extends Mage_Core_Model_Resource_Helper_Mssql
{
    /**
     * Get comparison condition for rule condition operator which will be used in SQL query
     *
     * @param string $operator
     * @return string
     */
    public function getSqlOperator($operator)
    {
        /*
            '{}'  => Mage::helper('Mage_Rule_Helper_Data')->__('contains'),
            '!{}' => Mage::helper('Mage_Rule_Helper_Data')->__('does not contain'),
            '()'  => Mage::helper('Mage_Rule_Helper_Data')->__('is one of'),
            '!()' => Mage::helper('Mage_Rule_Helper_Data')->__('is not one of'),
            requires custom selects
        */

        switch ($operator) {
            case '==':
                return '=';
            case '!=':
                return '<>';
            case '{}':
                return 'LIKE';
            case '!{}':
                return 'NOT LIKE';
            case '()':
                return 'IN';
            case '!()':
                return 'NOT IN';
            case '[]':
                return 'dbo.FIND_IN_SET(%s, %s) = 1';
            case '![]':
                return 'dbo.FIND_IN_SET(%s, %s) = 0';
            case 'between':
                return "BETWEEN '%s' AND '%s'";
            case '>':
            case '<':
            case '>=':
            case '<=':
                return $operator;
            default:
                Mage::throwException(Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Unknown operator specified.'));
        }
    }

    /**
     * Set Mssql specific limit
     *
     * @param Varien_Db_Select $select
     * @return Enterprise_CustomerSegment_Model_Resource_Helper_Mssql
     */
    public function setOneRowLimit(Varien_Db_Select $select)
    {
        $select->limit(1);
        return $this;
    }
}
