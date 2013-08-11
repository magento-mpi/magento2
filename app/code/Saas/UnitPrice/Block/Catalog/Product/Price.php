<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_UnitPrice_Block_Catalog_Product_Price extends Magento_Catalog_Block_Product_Price
{
    protected function _toHtml()
    {
        $html  = parent::_toHtml();
        $block = Mage::getBlockSingleton('Magento_Catalog_Block_Product_Price');
        $block->setProduct($this->getProduct());
        $block->setTemplate('Saas_UnitPrice::unitprice.phtml');
        $html .= $block->toHtml();

        return $html;
    }
}
