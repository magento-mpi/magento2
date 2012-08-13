<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Urlrewrite_Link extends Mage_Core_Block_Abstract
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
