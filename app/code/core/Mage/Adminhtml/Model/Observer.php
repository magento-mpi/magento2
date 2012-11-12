<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backend event observer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 * @deprecated Moved to module Mage_Backend
 */
class Mage_Adminhtml_Model_Observer extends Mage_Backend_Model_Observer
{
    /**
     * Change product type on the fly depending on selected options
     *
     * @param  Varien_Object $observer
     */
    public function transitionProductType($observer)
    {
        $product = $observer->getProduct();
        $isTransitionalType = in_array($product->getTypeId(), array(
                Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL)
        );
        if ($isTransitionalType) {
            $product->setTypeId($product->hasIsVirtual()
                    ? Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL
                    : Mage_Catalog_Model_Product_Type::TYPE_SIMPLE
            );
        }
    }
}
