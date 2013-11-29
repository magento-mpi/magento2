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
class AccountLink extends \Magento\View\Element\Html\Link\Current
{
    /** @var \Magento\Reward\Helper\Data */
    protected $_rewardHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\App\DefaultPathInterface $defaultPath
     * @param \Magento\Reward\Helper\Data $rewardHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\App\DefaultPathInterface $defaultPath,
        \Magento\Reward\Helper\Data $rewardHelper,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $defaultPath, $data);
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
