<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Block\Html;

/**
 * Html page welcome block
 */
class Welcome extends \Magento\Framework\View\Element\Template
{
    /**
     * Get block message
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->_layout->getBlock('header')->getWelcome();
    }
}
