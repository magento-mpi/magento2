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
namespace Magento\Log\Model\Resource;

class Shell
{
    /**
     * @var \Magento\Core\Model\Resource
     */
    protected $_resource;

    /**
     * @var \Magento\Core\Model\Resource\HelperFactory
     */
    protected $_helperPool;

    /**
     * @param \Magento\Core\Model\Resource\HelperPool $helperPool
     * @param \Magento\Core\Model\Resource $resource
     */
    public function __construct(
        \Magento\Core\Model\Resource\HelperPool $helperPool,
        \Magento\Core\Model\Resource $resource
    ) {
        $this->_helperPool = $helperPool;
        $this->_resource = $resource;
    }

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

        $resHelper = $this->_helperPool->get('Magento_Log');
        $result = array();
        foreach ($tables as $table) {
            $info = $resHelper->getTableInfo($this->_resource->getTableName($table));
            if (!$info) {
                continue;
            }
            $result[] = $info;
        }

        return $result;
    }
}
