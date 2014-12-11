<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Enterprise CustomerSegment Resource Helper Mysql
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerSegment\Model\Resource;

class Helper extends \Magento\Framework\DB\Helper
{
    /**
     * Get comparison condition for rule condition operator which will be used in SQL query
     *
     * @param string $operator
     * @throws \Magento\Framework\Model\Exception
     * @return string
     */
    public function getSqlOperator($operator)
    {
        /*
            '{}'  => __('contains'),
            '!{}' => __('does not contain'),
            '()'  => __('is one of'),
            '!()' => __('is not one of'),
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
                return 'FIND_IN_SET(%s, %s)';
            case '![]':
                return 'FIND_IN_SET(%s, %s) IS NULL';
            case 'between':
                return 'BETWEEN %s AND %s';
            case '>':
            case '<':
            case '>=':
            case '<=':
                return $operator;
            default:
                throw new \Magento\Framework\Model\Exception(__('Unknown operator specified.'));
        }
    }
}
