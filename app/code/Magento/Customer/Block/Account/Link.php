<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Account;

class Link extends \Magento\View\Element\Html\Link
{
    /**
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Data $customerHelper,
        array $data = array()
    ) {
        $this->_customerHelper = $customerHelper;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_customerHelper->getAccountUrl();
    }
}
