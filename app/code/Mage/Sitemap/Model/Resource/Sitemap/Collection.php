<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sitemap resource model collection
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sitemap_Model_Resource_Sitemap_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init collection
     *
     */
    public function _construct()
    {
        $this->_init('Mage_Sitemap_Model_Sitemap', 'Mage_Sitemap_Model_Resource_Sitemap');
    }

    /**
     * Filter collection by specified store ids
     *
     * @param array|int $storeIds
     * @return Mage_Sitemap_Model_Resource_Sitemap_Collection
     */
    public function addStoreFilter($storeIds)
    {
        $this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
        return $this;
    }
}
