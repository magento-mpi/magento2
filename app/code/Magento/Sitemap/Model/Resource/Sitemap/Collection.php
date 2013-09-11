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
namespace Magento\Sitemap\Model\Resource\Sitemap;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Init collection
     *
     */
    public function _construct()
    {
        $this->_init('\Magento\Sitemap\Model\Sitemap', '\Magento\Sitemap\Model\Resource\Sitemap');
    }

    /**
     * Filter collection by specified store ids
     *
     * @param array|int $storeIds
     * @return \Magento\Sitemap\Model\Resource\Sitemap\Collection
     */
    public function addStoreFilter($storeIds)
    {
        $this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
        return $this;
    }
}
