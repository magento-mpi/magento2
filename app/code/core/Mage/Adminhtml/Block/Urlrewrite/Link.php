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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
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
        if ($this->getItem()) {
            return '<p>' . $this->getLabel() . ' <a href="' . $this->getItemUrl() . '">'
                . $this->escapeHtml($this->getItem()->getName()) . '</a></p>';
        }
    }
}
