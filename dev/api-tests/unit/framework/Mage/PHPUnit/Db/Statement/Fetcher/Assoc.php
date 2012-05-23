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
 * Statement fetcher. Works with FETCH_ROW fetch setting.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Db_Statement_Fetcher_Assoc implements Mage_PHPUnit_Db_Statement_Fetcher_Interface
{
    /**
     * Returns formatted result row.
     *
     * @param array|bool $row
     * @return array|string|bool
     */
    public function fetch($row)
    {
        return $row;
    }
}
