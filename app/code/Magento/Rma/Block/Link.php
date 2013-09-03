<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * RMA Return Block
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Link extends Magento_Page_Block_Link_Current
{
    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if (Mage::helper('Magento_Rma_Helper_Data')->isEnabled()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
