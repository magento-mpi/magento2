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
     * @param \Magento\App\DefaultPathInterface $defaultPath
     * @param \Magento\Reward\Helper\Data $rewardHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\App\DefaultPathInterface $defaultPath,
        \Magento\Reward\Helper\Data $rewardHelper,
        array $data = array()
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->_rewardHelper = $rewardHelper;
        $this->_isScopePrivate = true;
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
