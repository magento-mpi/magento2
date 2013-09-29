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
 * Reward Points Settings form
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Block\Customer\Reward;

class Subscription extends \Magento\Core\Block\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Getter for RewardUpdateNotification
     *
     * @return bool
     */
    public function isSubscribedForUpdates()
    {
        return (bool)$this->_getCustomer()->getRewardUpdateNotification();
    }

    /**
     * Getter for RewardWarningNotification
     *
     * @return bool
     */
    public function isSubscribedForWarnings()
    {
        return (bool)$this->_getCustomer()->getRewardWarningNotification();
    }

    /**
     * Retrieve customer model
     *
     * @return \Magento\Customer\Model\Customer
     */
    protected function _getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }
}
