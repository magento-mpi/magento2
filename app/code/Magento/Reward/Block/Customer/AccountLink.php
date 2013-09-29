<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Block\Customer;

/**
 * "Reward Points" link
 */
class AccountLink extends \Magento\Page\Block\Link\Current
{
    /** @var \Magento\Reward\Helper\Data */
    protected $_rewardHelper;

    /**
     * Constructor
     *
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Reward\Helper\Data $rewardHelper
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Reward\Helper\Data $rewardHelper,
        \Magento\Core\Helper\Data $coreData,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_rewardHelper = $rewardHelper;
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if ($this->_rewardHelper->isEnabledOnFront()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
