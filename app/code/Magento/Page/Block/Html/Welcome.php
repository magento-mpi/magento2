<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Html page block
 *
 * @category   Magento
 * @package    Magento_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Page_Block_Html_Welcome extends Magento_Core_Block_Template
{
    /**
     * Get block messsage
     *
     * @return string
     */
    protected function _toHtml()
    {
        return Mage::app()->getLayout()->getBlock('header')->getWelcome();
    }
}
