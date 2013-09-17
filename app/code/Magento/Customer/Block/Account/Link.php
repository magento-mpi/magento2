<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Customer_Block_Account_Link extends Magento_Page_Block_Link
{
    /**
     * @return string
     */
    public function getHref()
    {
        return $this->helper('Magento_Customer_Helper_Data')->getAccountUrl();
    }
}