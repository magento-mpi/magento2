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
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * LoadTest Renderer Catalog model
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author     Victor Tihonchuk <victor@varien.com>
 */

class Mage_LoadTest_Model_Renderer_Catalog extends Mage_LoadTest_Model_Renderer_Abstract
{
    /**
     * Store collection
     *
     * @var Mage_Core_Model_Mysql4_Store_Collection
     */
    protected $_stores;

    /**
     * Category Ids array
     *
     * @var array
     */
    protected $_categoryIds;

    /**
     * Tax class collection
     *
     * @var Mage_Tax_Model_Mysql4_Class_Collection
     */
    protected $_tax_classes;

    /**
     * Processed (created/deleted) categories
     *
     * @var array
     */
    public $categories;

    /**
     * Processed (created/deleted) products
     *
     * @var array
     */
    public $products;

    /**
     * Init model
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setType('PRODUCT');
        $this->setSroreIds(null);
        $this->setNesting(2);
        $this->setMinCount(2);
        $this->setMaxCount(5);
        $this->setCountProducts(100);
        $this->setMinPrice(10);
        $this->setMaxPrice(300);
        $this->setMinWeight(10);
        $this->setMaxWeight(999);
        $this->setVisibility(4);
        $this->setQty(5);
        $this->setAttributeSetId(5);
        $this->setStartProductName(0);
    }

    /**
     * Render Products/Categories
     *
     * @return Mage_LoadTest_Model_Renderer_Catalog
     */
    public function render()
    {
        if ($this->getType() == 'PRODUCT')
        {
            $this->products = array();
            for ($i = 1; $i <= $this->getCountProducts(); $i ++) {
                $this->_createProduct($i + $this->getStartProductName());
            }
        }
        else {
            $this->categories = array();
            $this->_nestCategory(0, 1, null);
            $this->_updateCategories();
        }
        return $this;
    }

    /**
     * Delete all Products/Categories
     *
     * @return Mage_LoadTest_Model_Renderer_Catalog
     */
    public function delete()
    {
        if ($this->getType() == 'PRODUCT')
        {
            $this->products = array();
            $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('name')
                ->load();
            foreach ($collection as $product) {
                $this->products[$product->getId()] = $product->getName();
                $this->_beforeUsedMemory();
                $product->delete();
                $this->_afterUsedMemory();
            }
        }
        else {
            $this->categories = array();
            $collection = Mage::getModel('catalog/category')
                ->setStoreId(0)
                ->getCollection()
                ->addAttributeToSelect('name')
                ->load();
            $deleted  = array();
            $toDelete = array();
            foreach ($collection as $category) {
                if ($category->getId() == 2) {
                    continue;
                }
                if (!isset($deleted[$category->getParentId()])) {
                    $deleted[$category->getId()] = true;
                    $toDelete[] = $category->getId();
                }
                else {
                    $deleted[$category->getId()] = true;
                }
                $parentId = $category->getParentId();
                $parentId = $parentId == 2 ? 0 : $parentId;
                $this->categories[$parentId][$category->getId()] = $category->getName();
            }
            foreach ($toDelete as $categoryId) {
                $this->_beforeUsedMemory();
                Mage::getModel('catalog/category')->load($categoryId)
                    ->delete();
                $this->_afterUsedMemory();
            }
        }

        return $this;
    }

    /**
     * Recursive call create categories
     *
     * @param int $parentId
     * @param int $nested
     * @param string $prefix
     *
     * @return Mage_LoadTest_Model_Renderer_Catalog
     */
    protected function _nestCategory($parentId, $nested = 1, $prefix = null)
    {
        for ($i = 0; $i < rand($this->getMinCount(), $this->getMaxCount()); $i++) {
            $thisPrefix = (!empty($prefix) ? $prefix.'.' : '') . ($i + 1);

            $categoryId = $this->_createCategory($parentId, $thisPrefix);

            if ($nested < $this->getNesting()) {
                $this->_nestCategory($categoryId, $nested + 1, $thisPrefix);
            }
        }

        return $this;
    }

    /**
     * Create category
     *
     * @param int $parentId
     * @param string $mask
     * @return int
     */
    protected function _createCategory($parentId, $mask)
    {
        if (is_null($this->_stores)) {
            $this->_stores = $this->getStores($this->getStoreIds());
        }

        $this->_beforeUsedMemory();

        $categoryName = Mage::helper('loadtest')->__('Catalog %s', $mask);
        $category = Mage::getModel('catalog/category');
        foreach ($this->_stores as $store) {
            if (!$parentId) {
                $catalogParentId = $store->getConfig(Mage_Catalog_Model_Category::XML_PATH_ROOT_ID);
            }
            else {
                $catalogParentId = $parentId;
            }
            $category->setStoreId($store->getId());
            $category->setParentId($catalogParentId);
            $category->setName($categoryName);
            $category->setDisplayMode('PRODUCTS');
            $category->setAttributeSetId($category->getDefaultAttributeSetId());
            $category->setIsActive(1);
            $category->setNotUpdateDepends(true);
            $category->save();
        }

        /**
         * Save for All Stores
         */
        $category->setStore(0);
        $category->save();

        $categoryId = $category->getId();
        unset($category);
        $this->categories[$parentId][$categoryId] = $categoryName;

        $this->_afterUsedMemory();

        return $categoryId;
    }

    /**
     * Update categories tree and urls
     *
     * @return Mage_LoadTest_Model_Renderer_Catalog
     */
    protected function _updateCategories()
    {
        if (is_null($this->_stores)) {
            $this->_stores = $this->getStores($this->getStoreIds());
        }

        $this->_beforeUsedMemory();

        $category = Mage::getModel('catalog/category');
        /* @var $category Mage_Catalog_Model_Category */
        $tree = $category->getTreeModel()
            ->load();
        $nodes = array();
        /* @var $tree Mage_Catalog_Model_Entity_Category_Tree */
        foreach ($tree->getNodes() as $nodeId => $node) {
            $nodes[$nodeId] = array(
                'path'          => array(),
                'children'      => array(),
                'children_all'  => array()
            );
            foreach ($node->getPath() as $path) {
                $nodes[$nodeId]['path'][] = $path->getId();
            }
            foreach ($node->getChildren() as $child) {
                $nodes[$nodeId]['children'][] = $child->getId();
            }

            foreach ($node->getAllChildNodes() as $child) {
                $nodes[$nodeId]['children_all'][] = $child->getId();
            }
        }

        $collection = $category->getCollection()
            ->load();
        foreach ($collection as $item) {
            $item->setData('path_in_store', join(',', $nodes[$item->getId()]['path']));
            $item->getResource()->saveAttribute($item, 'path_in_store');

            $item->setData('children', join(',', $nodes[$item->getId()]['children']));
            $item->getResource()->saveAttribute($item, 'children');

            $item->setData('all_children', join(',', $nodes[$item->getId()]['children_all']));
            $item->getResource()->saveAttribute($item, 'all_children');

            foreach ($this->_stores as $store) {
                $catalogParentId = $store->getConfig(Mage_Catalog_Model_Category::XML_PATH_ROOT_ID);
                $deep = true;
                $pathIds = array();
                foreach ($nodes[$item->getId()]['path'] as $path) {
                    if (!$deep) {
                        continue;
                    }
                    if ($path == $catalogParentId) {
                        $deep = false;
                        continue;
                    }
                    $pathIds[] = $path;
                }
                $item->setStore($store->getId());
                $item->setData('path_in_store', join(',', $pathIds));
                $item->getResource()->saveAttribute($item, 'path_in_store');
            }
        }

        unset($collection);
        unset($nodes);

        Mage::getSingleton('catalog/url')->refreshRewrites();

        $this->_afterUsedMemory();

        return $this;
    }

    /**
     * Create product
     *
     * @param int $mask
     * @return int
     */
    protected function _createProduct($mask)
    {
        if (is_null($this->_categoryIds)) {
            $collection = Mage::getModel('catalog/category')
                ->getCollection()
                ->load();
            $this->_categoryIds = $collection;
            foreach ($collection as $category) {
                $this->_categoryIds[$category->getId()] = $category->getId();
            }
        }
        if (is_null($this->_stores)) {
            $this->_stores = $this->getStores($this->getStoreIds());
        }
        if (is_null($this->_tax_classes)) {
            $this->_tax_classes = Mage::getModel('tax/class')
                ->getCollection()
                ->setClassTypeFilter('PRODUCT');
        }

        $this->_beforeUsedMemory();

        $productName = Mage::helper('loadtest')->__('Product #%s', $mask);
        $productDescription = Mage::helper('loadtest')->__('Description for Product #%s', $mask);
        $productShortDescription = Mage::helper('loadtest')->__('Short description for Product #%s', $mask);
        $productSku = $this->_getSku($mask);
        $stockData = array(
            'qty'               => $this->getQty(),
            'min_qty'           => 0,
            'min_sale_qty'      => 0,
            'max_sale_qty'      => $this->getQty(),
            'is_qty_decimal'    => 0,
            'backorders'        => 0,
            'is_in_stock'       => 1
        );
        $stores = array();
        foreach ($this->_stores as $store) {
            $stores[$store->getId()] = 0;
        }
        $categories = array_rand($this->_categoryIds, rand($this->getMinCount(), $this->getMaxCount()));
        $taxClass = 0;
        foreach ($this->_tax_classes as $class) {
            if (!$taxClass) {
                $taxClass = $class->getId();
            }
            else {
                if (rand(1,0) == 1) {
                    $taxClass = $class->getId();
                }
            }
        }

        $product = Mage::getModel('catalog/product')
            ->setAttributeSetId($this->getAttributeSetId())
            ->setTypeId(1)
            ->setStoreId(0)
            ->setName($productName)
            ->setDescription($productDescription)
            ->setShortDescription($productShortDescription)
            ->setSku($productSku)
            ->setWeight(rand($this->getMinWeight(), $this->getMaxWeight()))
            ->setStatus(1)
            ->setVisibility($this->getVisibility())
            ->setGiftMessageAvailable(1)
            ->setPrice(rand($this->getMinPrice(), $this->getMaxPrice()))
            ->setStockData($stockData)
            ->setTaxClassId($taxClass)
            ->setPostedStores($stores)
            ->setPostedCategories($categories);

        $product->save();

        $productId = $product->getId();

        if ($product->getStoresChangedFlag()) {
             Mage::dispatchEvent('catalog_controller_product_save_visibility_changed', array('product'=>$product));
        }

        $this->products[$productId] = $product->getName();

        unset($product);

        $this->_afterUsedMemory();

        return $productId;
    }

    /**
     * Get Unique generated SKU
     *
     * @param int $number
     * @return string
     */
    protected function _getSku($number)
    {
        $length = 8;
        $str    = '';
        $rnd    = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
        $len    = count($rnd) - 1;

        for ($i = 0; $i < $length; $i ++) {
            $str .= $rnd[rand(0, $len)];
        }

        return $str . '-' . $number;
    }
}