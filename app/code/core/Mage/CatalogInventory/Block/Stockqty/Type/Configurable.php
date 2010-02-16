<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product stock qty block for configurable product type
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Block_Stockqty_Type_Configurable extends Mage_CatalogInventory_Block_Stockqty_Abstract
{
    /**
     * Retrieve attributes
     *
     * @return array
     */
    protected function _getAllowAttributes()
    {
        return $this->_getProduct()->getTypeInstance(true)
            ->getConfigurableAttributes($this->_getProduct());
    }

    /**
     * Retrieve all child products which is in stock
     *
     * @return array
     */
    protected function _getAllowProducts()
    {
        if (!$this->hasAllowProducts()) {
            $products = array();
            $allProducts = $this->_getProduct()->getTypeInstance(true)
                ->getUsedProducts(null, $this->_getProduct());
            foreach ($allProducts as $product) {
                if ($product->isSaleable()) {
                    $products[] = $product;
                }
            }
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }

    /**
     * Retrieve json config to be passed to javascript object
     *
     * @return string
     */
    public function getJsonConfig()
    {
        // prepare product ids by attributeId & attributeOption
        $productsByAttrOpt = array();
        foreach ($this->_getAllowAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
            $productsByOpt = array();
            foreach ($this->_getAllowProducts() as $product) {
                $attributeOption = $product->getData($productAttribute->getAttributeCode());
                if (!array_key_exists($attributeOption, $productsByOpt)) {
                    $productsByOpt[$attributeOption] = array();
                }
                $productsByOpt[$attributeOption][] = $product->getId();
            }
            if(count($productsByOpt) > 0) {
               $productsByAttrOpt[$attributeId] = $productsByOpt;
            }
        }
        // prepare hash array productId => qty
        $qtyByProductId = array();
        foreach ($this->_getAllowProducts() as $product) {
            $qtyByProductId[$product->getId()] = ($product->hasStockItem() ? $product->getStockItem()->getStockQty() : 0);
        }
        $config = array(
            'initialStockQty'   => $this->getStockQty(),
            'productsByAttrOpt' => $productsByAttrOpt,
            'qtyByProductId'    => $qtyByProductId,
            'thresholdQty'      => $this->getThresholdQty(),
            'placeholderId'     => $this->getPlaceholderId(),
        );
        return Mage::helper('core')->jsonEncode($config);
    }
}
