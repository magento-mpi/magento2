<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Model\Resource\Balance;

/**
 * Customerbalance history collection
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\CustomerBalance\Model\Balance', 'Magento\CustomerBalance\Model\Resource\Balance');
    }

    /**
     * Filter collection by specified websites
     *
     * @param string $websiteIds
     * @return $this
     */
    public function addWebsitesFilter($websiteIds)
    {
        $this->getSelect()->where('main_table.website_id IN (?)', $websiteIds);
        return $this;
    }

    /**
     * Implement after load logic for each collection item
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $this->walk('afterLoad');
        return $this;
    }
}
