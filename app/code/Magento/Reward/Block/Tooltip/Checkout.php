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
 * Checkout Tooltip block to show checkout cart message for gaining reward points
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Block\Tooltip;

class Checkout extends \Magento\Reward\Block\Tooltip
{
    /**
     * Set quote to the reward action instance
     *
     * @param int|string $action
     */
    public function initRewardType($action)
    {
        parent::initRewardType($action);
        if ($this->_actionInstance) {
            $this->_actionInstance->setQuote(\Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote());
        }
    }
}
