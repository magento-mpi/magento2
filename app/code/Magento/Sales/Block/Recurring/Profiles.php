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
namespace Magento\Sales\Block\Recurring;

class Profiles extends \Magento\Core\Block\Template
{

    /**
     * Set back Url
     *
     * @return \Magento\Sales\Block\Recurring\Profiles
     */
    protected function _beforeToHtml()
    {
        $this->setBackUrl($this->getUrl('customer/account/'));
        return parent::_beforeToHtml();
    }
}
