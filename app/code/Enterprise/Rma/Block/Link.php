<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * RMA Return Block
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Link extends Mage_Page_Block_Link_Current
{
    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if (Mage::helper('Enterprise_Rma_Helper_Data')->isEnabled()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
