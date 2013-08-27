<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Remember Me block
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Persistent_Block_Header_Additional extends Magento_Core_Block_Html_Link
{
    /**
     * Render additional header html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $text = __('(Not %1?)', $this->escapeHtml(Mage::helper('Magento_Persistent_Helper_Session')->getCustomer()->getName()));

        $this->setAnchorText($text);
        $this->setHref($this->getUrl('persistent/index/unsetCookie'));

        return parent::_toHtml();
    }
}
