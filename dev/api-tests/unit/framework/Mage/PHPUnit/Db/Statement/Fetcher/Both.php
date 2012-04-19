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
 * Statement fetcher. Works with FETCH_BOTH fetch setting.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Db_Statement_Fetcher_Both implements Mage_PHPUnit_Db_Statement_Fetcher_Interface
{
    /**
     * Returns formatted result row.
     *
     * @param array|bool $row
     * @return array|string|bool
     */
    public function fetch($row)
    {
        if (is_array($row)) {
            $result = array();
            foreach ($row as $column => $value) {
                $result[$column] = $value;
                $result[] = $value;
            }
            return $result;
        }
        return $row;
    }
}
