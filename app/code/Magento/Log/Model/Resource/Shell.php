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
     * @var \Magento\App\Resource
     */
    protected $_resource;

    /**
     * @var \Magento\Log\Model\Resource\Helper
     */
    protected $_resourceHelper;

    /**
     * @param \Magento\Log\Model\Resource\Helper $resourceHelper
     * @param \Magento\App\Resource $resource
     */
    public function __construct(
        \Magento\Log\Model\Resource\Helper $resourceHelper,
        \Magento\App\Resource $resource
    ) {
        $this->_resourceHelper = $resourceHelper;
        $this->_resource = $resource;
    }

    /**
     * Retrieves information about log tables
     *
     * @return string[]
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

        $result = array();
        foreach ($tables as $table) {
            $info = $this->_resourceHelper->getTableInfo($this->_resource->getTableName($table));
            if (!$info) {
                continue;
            }
            $result[] = $info;
        }

        return $result;
    }
}
