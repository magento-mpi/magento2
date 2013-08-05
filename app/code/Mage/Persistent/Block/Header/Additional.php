<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Remember Me block
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Persistent_Block_Header_Additional extends Mage_Core_Block_Html_Link
{
    /**
     * Render additional header html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $text = __('(Not %s?)', $this->escapeHtml(Mage::helper('Mage_Persistent_Helper_Session')->getCustomer()->getName()));

        $this->setAnchorText($text);
        $this->setHref($this->getUrl('persistent/index/unsetCookie'));

        return parent::_toHtml();
    }
}
