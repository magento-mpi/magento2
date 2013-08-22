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
 * Reward rate collection
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Resource_Reward_Rate_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        $this->_init('Enterprise_Reward_Model_Reward_Rate', 'Enterprise_Reward_Model_Resource_Reward_Rate');
    }

    /**
     * Add filter by website id
     *
     * @param integer|array $websiteId
     * @return Enterprise_Reward_Model_Resource_Reward_Rate_Collection
     */
    public function addWebsiteFilter($websiteId)
    {
        $websiteId = array_merge((array)$websiteId, array(0));
        $this->getSelect()->where('main_table.website_id IN (?)', $websiteId);
        return $this;
    }
}
