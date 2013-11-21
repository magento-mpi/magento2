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
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Reward\Helper\Data $rewardHelper
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\App\DefaultPathInterface $defaultPath
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Template\Context $context,
        \Magento\Reward\Helper\Data $rewardHelper,
        \Magento\Core\Helper\Data $coreData,
        \Magento\App\DefaultPathInterface $defaultPath,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $defaultPath, $data);
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
