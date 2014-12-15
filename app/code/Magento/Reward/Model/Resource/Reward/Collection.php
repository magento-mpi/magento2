<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Model\Resource\Reward;

/**
 * Reward collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Reward\Model\Reward', 'Magento\Reward\Model\Resource\Reward');
    }

    /**
     * Add filter by website id
     *
     * @param int|array $websiteId
     * @return $this
     */
    public function addWebsiteFilter($websiteId)
    {
        $this->getSelect()->where(
            is_array($websiteId) ? 'main_table.website_id IN (?)' : 'main_table.website_id = ?',
            $websiteId
        );
        return $this;
    }
}
