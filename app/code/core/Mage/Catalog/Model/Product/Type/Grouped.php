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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Grouped product type implementation
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Product_Type_Grouped extends Mage_Catalog_Model_Product_Type_Abstract
{
    protected $_associatedProducts  = null;
    protected $_associatedProductIds= null;

    /**
     * Retrieve product type attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if (!$this->_attributes) {
            $this->_attributes = parent::getAttributes();
            foreach ($this->_attributes as $index => $attribute) {
                if (!$attribute->getUseInSuperProduct()) {
                    unset($this->_attributes[$index]);
                }
            }
        }
        return $this->_attributes;
    }

    /**
     * Retrieve array of associated products
     *
     * @return array
     */
    public function getAssociatedProducts()
    {
        if (is_null($this->_associatedProducts)) {
            $this->_associatedProducts = array();
            foreach ($this->getAssociatedProductCollection() as $product) {
            	$this->_associatedProducts[] = $product;
            }
        }
        return $this->_associatedProducts;
    }

    /**
     * Retrieve related products identifiers
     *
     * @return array
     */
    public function getAssociatedProductIds()
    {
        if (is_null($this->_associatedProductIds)) {
            $this->_associatedProductIds = array();
            foreach ($this->getAssociatedProducts() as $product) {
            	$this->_associatedProductIds[] = $product->getId();
            }
        }
        return $this->_associatedProductIds;
    }

    /**
     * Retrieve collection of associated products
     */
    public function getAssociatedProductCollection()
    {
        $collection = $this->getProduct()->getLinkInstance()->useGroupedLinks()
            ->getProductCollection()
            ->setIsStrongMode();
        $collection->setProduct($this->getProduct());
        return $collection;
    }

    /**
     * Save type related data
     *
     * @return unknown
     */
    public function save()
    {
        parent::save();
        $this->getProduct()->getLinkInstance()->saveGroupedLinks($this->getProduct());
        return $this;
    }
}