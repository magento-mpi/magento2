<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Block_Product_Grouped_AssociatedProducts extends Mage_Backend_Block_Catalog_Product_Tab_Container
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('grouped_product_container');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Mage_Catalog_Helper_Data')->__('Grouped Products');
    }

    /**
     * Get parent tab code
     *
     * @return string
     */
    public function getParentTab()
    {
        return 'product-details';
    }
}
