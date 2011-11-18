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
class Enterprise_Reward_Model_Resource_Reward_Rate_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        $this->_init('Enterprise_Reward_Model_Reward_Rate', 'Enterprise_Reward_Model_Resource_Reward_Rate');
    }
}
