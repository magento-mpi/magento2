<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product stock qty default block
 *
 * @category   Magento
 * @package    Magento_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogInventory_Block_Stockqty_Default extends Magento_CatalogInventory_Block_Stockqty_Abstract
{
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->isMsgVisible()) {
            return '';
        }
        return parent::_toHtml();
    }
}
