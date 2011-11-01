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
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product related items block
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Bundle_Block_Catalog_Product_List_Partof extends Mage_Catalog_Block_Product_Abstract
{
    protected $_columnCount = 4;
    protected $_items;
    protected $_itemCollection;
    protected $_product = null;

    protected function _prepareData()
    {
        $collection = Mage::getModel('Mage_Catalog_Model_Product')->getResourceCollection()
            ->addAttributeToSelect(Mage::getSingleton('Mage_Catalog_Model_Config')->getProductAttributes())
            ->addAttributeToSort('position', 'asc')
            ->addStoreFilter()
            ->addMinimalPrice()

            ->joinTable('catalog_product_bundle_option', 'parent_id=entity_id', array('option_id' => 'option_id'))
            ->joinTable('catalog_product_bundle_selection', 'option_id=option_id', array('product_id' => 'product_id'), '{{table}}.product_id='.$this->getProduct()->getId());

            $ids = Mage::getSingleton('Mage_Checkout_Model_Cart')->getProductIds();

            if (count($ids)) {
                $collection->addIdFilter(Mage::getSingleton('Mage_Checkout_Model_Cart')->getProductIds(), true);
            }

        Mage::getSingleton('Mage_Catalog_Model_Product_Status')->addSaleableFilterToCollection($collection);
        Mage::getSingleton('Mage_Catalog_Model_Product_Visibility')->addVisibleInCatalogFilterToCollection($collection);
        $collection->getSelect()->group('entity_id');

        $collection->load();
        $this->_itemCollection = $collection;

        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    public function getItemCollection()
    {
        return $this->_itemCollection;
    }

    public function getItems() {
        if (is_null($this->_items)) {
            $this->_items = $this->getItemCollection()->getItems();
        }
        return $this->_items;
    }

    public function getRowCount()
    {
        return ceil($this->getItemCollection()->getSize()/$this->getColumnCount());
    }

    public function setColumnCount($columns)
    {
        if (intval($columns) > 0) {
            $this->_columnCount = intval($columns);
        }
        return $this;
    }

    public function getColumnCount()
    {
        return $this->_columnCount;
    }

    public function resetItemsIterator()
    {
        $this->getItems();
        reset($this->_items);
    }

    public function getIterableItem()
    {
        $item = current($this->_items);
        next($this->_items);
        return $item;
    }

    /**
     * Get current product from registry
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = Mage::registry('product');
        }
        return $this->_product;
    }
}
