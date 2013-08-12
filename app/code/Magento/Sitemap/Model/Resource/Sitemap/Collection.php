<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sitemap resource model collection
 *
 * @category    Magento
 * @package     Magento_Sitemap
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sitemap_Model_Resource_Sitemap_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init collection
     *
     */
    public function _construct()
    {
        $this->_init('Magento_Sitemap_Model_Sitemap', 'Magento_Sitemap_Model_Resource_Sitemap');
    }

    /**
     * Filter collection by specified store ids
     *
     * @param array|int $storeIds
     * @return Magento_Sitemap_Model_Resource_Sitemap_Collection
     */
    public function addStoreFilter($storeIds)
    {
        $this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
        return $this;
    }
}
