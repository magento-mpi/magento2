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
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Reward\Helper\Data $rewardHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Reward\Model\Reward $rewardInstance
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Reward\Helper\Data $rewardHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Reward\Model\Reward $rewardInstance,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Core\Helper\Data $coreData,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $rewardHelper,
            $customerSession,
            $rewardInstance,
            $storeManager,
            $coreData,
            $data
        );
        $this->_checkoutSession = $checkoutSession;
    }


    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->_actionInstance) {
            $this->_actionInstance->setQuote($this->_checkoutSession->getQuote());
        }
        return $this;
    }
}
