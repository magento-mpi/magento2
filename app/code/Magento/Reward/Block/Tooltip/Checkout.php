<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Checkout Tooltip block to show checkout cart message for gaining reward points
 */
namespace Magento\Reward\Block\Tooltip;

class Checkout extends \Magento\Reward\Block\Tooltip
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Reward\Helper\Data $rewardHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Reward\Model\Reward $rewardInstance
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Reward\Helper\Data $rewardHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Reward\Model\Reward $rewardInstance,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context, $rewardHelper, $customerSession, $rewardInstance, $data);
    }

    /**
     * @return $this|\Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->_actionInstance) {
            $this->_actionInstance->setQuote($this->_checkoutSession->getQuote());
        }
        return $this;
    }
}
