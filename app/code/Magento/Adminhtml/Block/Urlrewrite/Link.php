<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Label & link block
 *
 * @method string getLabel()
 * @method string getItemUrl()
 * @method string getItemName()
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Urlrewrite_Link extends Magento_Core_Block_Abstract
{
    /**
     * Render output
     *
     * @return string
     */
    protected function _toHtml()
    {
        return '<p>' . $this->getLabel() . ' <a href="' . $this->getItemUrl() . '">'
            . $this->escapeHtml($this->getItemName()) . '</a></p>';
    }
}
