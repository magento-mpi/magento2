<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Bundle_Block_Backend_Items extends Mage_Backend_Block_Catalog_Product_Tab_Container
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('bundle_product_container');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Mage_Catalog_Helper_Data')->__('Bundle Items');
    }
}
