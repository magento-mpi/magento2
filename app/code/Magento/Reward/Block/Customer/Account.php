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
 * Customer Account empty block (using only just for adding RP link to tab)
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Block\Customer;

class Account extends \Magento\Core\Block\AbstractBlock
{
    /**
     * Add RP link to tab if we have all rates
     *
     * @return \Magento\Reward\Block\Customer\Account
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var $navigationBlock \Magento\Customer\Block\Account\Navigation */
        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock && \Mage::helper('Magento\Reward\Helper\Data')->isEnabledOnFront()) {
            $navigationBlock->addLink('magento_reward', 'magento_reward/customer/info/',
                __('Reward Points')
            );
        }
        return $this;
    }
}
