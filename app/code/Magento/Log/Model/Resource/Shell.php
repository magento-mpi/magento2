<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource model for commands, executed in shell
 *
 * @category    Magento
 * @package     Magento_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Log_Model_Resource_Shell
{
    /**
     * Retrieves information about log tables
     *
     * @return array
     */
    public function getTablesInfo()
    {
        $tables = array(
            'log_customer',
            'log_visitor',
            'log_visitor_info',
            'log_url_table',
            'log_url_info_table',
            'log_quote_table',
            'reports_viewed_product_index',
            'reports_compared_product_index',
            'reports_event',
            'catalog_compare_item'
        );

        $resHelper = Mage::getResourceHelper('Magento_Log');
        $result = array();
        $resource = Mage::getSingleton('Magento_Core_Model_Resource');
        foreach ($tables as $table) {
            $info = $resHelper->getTableInfo($resource->getTableName($table));
            if (!$info) {
                continue;
            }
            $result[] = $info;
        }

        return $result;
    }
}
