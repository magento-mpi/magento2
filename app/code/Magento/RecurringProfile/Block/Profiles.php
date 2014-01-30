<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profiles listing
 */
namespace Magento\RecurringProfile\Block;

class Profiles extends \Magento\View\Element\Template
{

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Set back Url
     *
     * @return \Magento\RecurringProfile\Block\Profiles
     */
    protected function _beforeToHtml()
    {
        $this->setBackUrl($this->getUrl('customer/account/'));
        return parent::_beforeToHtml();
    }
}
