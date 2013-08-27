<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Frontend helper block to add links
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 */
class Enterprise_Checkout_Block_Customer_Link extends Mage_Page_Block_Link_Current
{
    /** @var Enterprise_Checkout_Helper_Data  */
    protected $_customerHelper;

    /**
     * Constructor
     *
     * @param Mage_Core_Block_Template_Context $context
     * @param Enterprise_Checkout_Helper_Data $customerHelper
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Enterprise_Checkout_Helper_Data $customerHelper,
        array $data = array()
    ) {
        parent::__construct($context, $data);
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
