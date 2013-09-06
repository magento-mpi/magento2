<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward rate collection
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Model_Resource_Reward_Rate_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Reward_Model_Reward_Rate', 'Magento_Reward_Model_Resource_Reward_Rate');
    }

    /**
     * Add filter by website id
     *
     * @param integer|array $websiteId
     * @return Magento_Reward_Model_Resource_Reward_Rate_Collection
     */
    public function addWebsiteFilter($websiteId)
    {
        $websiteId = array_merge((array)$websiteId, array(0));
        $this->getSelect()->where('main_table.website_id IN (?)', $websiteId);
        return $this;
    }
}
