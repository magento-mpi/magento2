<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Account;

class Link extends \Magento\Page\Block\Link
{
    /**
     * @return string
     */
    public function getHref()
    {
        return $this->helper('Magento_Customer_Helper_Data')->getAccountUrl();
    }
}
