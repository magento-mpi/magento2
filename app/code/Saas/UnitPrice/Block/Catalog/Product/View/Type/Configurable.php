<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product View block
 *
 * @category Saas
 * @package  Saas_UnitPrice
 */
class Saas_UnitPrice_Block_Catalog_Product_View_Type_Configurable
    extends Mage_Catalog_Block_Product_View_Type_Configurable
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        $autoAppend = Mage::helper('Saas_UnitPrice_Helper_Data')
            ->getConfig('auto_append_unit_price');

        return ($autoAppend)
            ? $this->_getBasePriceHtml($this->getProduct())
            : '';
    }

    /**
     * Returns product base price block html
     *
     * @param Mage_Catalog_Model_Product $product Current product
     *
     * @return string HTML code
     */
    protected function _getBasePriceHtml($product)
    {
        return $this->getLayout()->createBlock('Magento_Core_Block_Template')
            ->setTemplate('Saas_UnitPrice::unitprice.phtml')
            ->setProduct($product)
            ->toHtml();
    }
}
