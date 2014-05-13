<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reward action for updating balance by administrator
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Model\Action;

class Admin extends \Magento\Reward\Model\Action\AbstractAction
{
    /**
     * Check whether rewards can be added for action
     *
     * @return bool
     */
    public function canAddRewardPoints()
    {
        return true;
    }

    /**
     * Return action message for history log
     *
     * @param array $args Additional history data
     * @return string
     */
    public function getHistoryMessage($args = array())
    {
        return __('Updated by moderator');
    }
}
