<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart downloadable item render block
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Block_Checkout_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{

    /**
     * Retrieves item links options
     *
     * @return array
     */
    public function getLinks()
    {
        return Mage::helper('Mage_Downloadable_Helper_Catalog_Product_Configuration')->getLinks($this->getItem());
    }

    /**
     * Return title of links section
     *
     * @return string
     */
    public function getLinksTitle()
    {
        return Mage::helper('Mage_Downloadable_Helper_Catalog_Product_Configuration')->getLinksTitle($this->getProduct());
    }
}
