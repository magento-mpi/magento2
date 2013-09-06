<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profiles listing
 */
class Magento_Sales_Block_Recurring_Profiles extends Magento_Core_Block_Template
{

    /**
     * Set back Url
     *
     * @return Magento_Sales_Block_Recurring_Profiles
     */
    protected function _beforeToHtml()
    {
        $this->setBackUrl($this->getUrl('customer/account/'));
        return parent::_beforeToHtml();
    }
}
