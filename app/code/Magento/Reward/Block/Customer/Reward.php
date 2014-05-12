<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer My Account -> Reward Points container
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Block\Customer;

class Reward extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Set template variables
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setBackUrl($this->getUrl('customer/account/'));
        return parent::_toHtml();
    }
}
