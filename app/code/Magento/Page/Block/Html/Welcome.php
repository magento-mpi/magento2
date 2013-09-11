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
namespace Magento\Page\Block\Html;

class Welcome extends \Magento\Core\Block\Template
{
    /**
     * Get block messsage
     *
     * @return string
     */
    protected function _toHtml()
    {
        return \Mage::app()->getLayout()->getBlock('header')->getWelcome();
    }
}
