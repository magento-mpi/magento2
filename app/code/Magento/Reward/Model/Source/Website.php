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
 * Source model for websites, including "All" option
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Model_Source_Website implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Prepare and return array of website ids and their names
     *
     * @param bool $withAll Whether to prepend "All websites" option on not
     * @return array
     */
    public function toOptionArray($withAll = true)
    {
        $websites = Mage::getSingleton('Magento_Core_Model_System_Store')->getWebsiteOptionHash();
        if ($withAll) {
            $websites = array(0 => __('All Websites'))
                      + $websites;
        }
        return $websites;
    }
}
