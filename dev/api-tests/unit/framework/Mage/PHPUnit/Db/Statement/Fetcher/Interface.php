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
 * Statement for local database adapter
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_PHPUnit_Db_Statement_Fetcher_Interface
{
    /**
     * Returns formatted result row or input row (in case if $row is null or false).
     *
     * @param array|bool $row
     * @return array|string|bool
     */
    public function fetch($row);
}
