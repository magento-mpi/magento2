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

class Link extends \Magento\Core\Block\Template
{
    /** @var Magento_AdvancedCheckout_Helper_Data  */
    protected $_customerHelper;

    /**
     * Constructor
     *
     * @var \Magento\AdvancedCheckout\Helper\Data
     */
    protected $_checkoutData = null;

    /**
     * @param \Magento\AdvancedCheckout\Helper\Data $checkoutData
     * @param Magento_AdvancedCheckout_Helper_Data $customerHelper
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Helper_Data $coreData,
        Magento_AdvancedCheckout_Helper_Data $customerHelper,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_customerHelper = $customerHelper;
    }

    /**
     * @inheritdoc
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
