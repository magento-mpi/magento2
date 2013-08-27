<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Customer_Block_Account_Link extends Mage_Page_Block_Link
{
    /**
     * @return string
     */
    public function getHref()
    {
        return $this->helper('Mage_Customer_Helper_Data')->getAccountUrl();
    }
}