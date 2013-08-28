<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward collection
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Resource_Reward_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Internal construcotr
     *
     */
    protected function _construct()
    {
        $this->_init('Enterprise_Reward_Model_Reward', 'Enterprise_Reward_Model_Resource_Reward');
    }

    /**
     * Add filter by website id
     *
     * @param integer|array $websiteId
     * @return Enterprise_Reward_Model_Resource_Reward_Collection
     */
    public function addWebsiteFilter($websiteId)
    {
        $this->getSelect()
            ->where(is_array($websiteId) ? 'main_table.website_id IN (?)' : 'main_table.website_id = ?', $websiteId);
        return $this;
    }
}
