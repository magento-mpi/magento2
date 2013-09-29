<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Frontend helper block to add links
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 */
namespace Magento\AdvancedCheckout\Block\Customer;

class Link extends \Magento\Page\Block\Link\Current
{
    /** @var \Magento\AdvancedCheckout\Helper\Data  */
    protected $_customerHelper;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\AdvancedCheckout\Helper\Data $customerHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\AdvancedCheckout\Helper\Data $customerHelper,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_customerHelper = $customerHelper;
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
