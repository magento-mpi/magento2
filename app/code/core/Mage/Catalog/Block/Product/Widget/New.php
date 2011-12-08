<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * New products widget
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_Widget_New
    extends Mage_Catalog_Block_Product_New
    implements Mage_Widget_Block_Interface
{
    /**
     * Internal contructor
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addPriceBlockType(
            'bundle',
            'Mage_Bundle_Block_Catalog_Product_Price',
            'bundle/catalog/product/price.phtml'
        );
    }

    /**
     * Retrieve how much products should be displayed.
     *
     * @return int
     */
    public function getProductsCount()
    {
        if (!$this->hasData('products_count')) {
            return parent::getProductsCount();
        }
        return $this->_getData('products_count');
    }
}
