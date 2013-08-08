<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Page_Block_Html_Welcome extends Magento_Core_Block_Template
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
