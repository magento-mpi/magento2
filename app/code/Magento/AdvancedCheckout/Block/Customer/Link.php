<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Frontend helper block to add links
 *
 */
namespace Magento\AdvancedCheckout\Block\Customer;

class Link extends \Magento\Framework\View\Element\Html\Link\Current
{
    /** @var \Magento\AdvancedCheckout\Helper\Data  */
    protected $_customerHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Magento\AdvancedCheckout\Helper\Data $customerHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\AdvancedCheckout\Helper\Data $customerHelper,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->_customerHelper = $customerHelper;
        $this->_isScopePrivate = true;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_customerHelper->isSkuApplied()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
