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
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData = null;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Core\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Core\Block\Context $context,
        array $data = array()
    ) {
        $this->_rewardData = $rewardData;
        parent::__construct($context, $data);
    }

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
        if ($navigationBlock && $this->_rewardData->isEnabledOnFront()) {
            $navigationBlock->addLink('magento_reward', 'magento_reward/customer/info/',
                __('Reward Points')
            );
        }
        return $this;
    }
}
