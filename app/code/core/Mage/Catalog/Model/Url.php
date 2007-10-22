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
 * Catalog url model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Catalog_Model_Url
{
    protected $_storeRoots;
    protected $_rewrites;
    protected $_categories;
    protected $_products;

    public function loadRewrites($storeId)
    {
        $rewriteCollection = Mage::getResourceModel('core/url_rewrite_collection');
        $rewriteCollection->getSelect()
            ->where("id_path like 'category/%' or id_path like 'product/%'")
            ->where("store_id=?", $storeId);
        $rewriteCollection->load();

        $this->_rewrites[$storeId] = array();
        foreach ($rewriteCollection as $rewrite) {
            $this->_rewrites[$rewrite->getStoreId()][$rewrite->getIdPath()] = $rewrite;
        }
        return $this;
    }

    public function loadCategories($storeId)
    {
        $categoryCollection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('url_path');
        $categoryCollection->getEntity()
            ->setStore($storeId);
        $categoryCollection->load();

        $this->_categories = array();
        foreach ($categoryCollection as $category) {
            $this->_categories[$storeId][$category->getId()] = $category;
        }

        foreach ($this->_categories[$storeId] as $categoryId=>$category) {
            $parent = $this->getCategory($storeId, $category->getParentId());
            if (!$parent) {
                continue;
            }
            $children = $parent->getChildren();
            $children[$categoryId] = $category;
            $parent->setChildren($children);
        }

        return $this;
    }

    public function loadProducts($storeId)
    {
        $productCollection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('url_key');
        $productCollection->getEntity()
            ->setStore($storeId);
        $productCollection->load();

        $this->_products[$storeId] = $productCollection->getItems();

        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('catalog_read');
        $productStoreTable = $resource->getTableName('catalog/product_store');
        $categoryProductTable = $resource->getTableName('catalog/category_product');

        $select = $read->select()
            ->from(array('cp'=>$categoryProductTable))
            ->join(array('ps'=>$productStoreTable), 'ps.product_id=cp.product_id', array())
            ->where('ps.store_id=?', $storeId);

        $categoryProducts = $read->fetchAll($select);
        foreach ($categoryProducts as $row) {
            $category = $this->getCategory($storeId, $row['category_id']);
            $product = $this->getProduct($storeId, $row['product_id']);
            if (!$category || !$product) {
                continue;
            }
            $products = $category->getProducts();
            $products[$product->getId()] = $product;
            $category->setProducts($products);
        }

        return $this;
    }

    public function getRootId($storeId=null)
    {
        if (!$this->_storeRoots) {
            foreach (Mage::getConfig()->getNode('stores')->children() as $storeNode) {
                $sId = (int)$storeNode->system->store->id;
                $rId = (int)$storeNode->catalog->category->root_id;
                if ($sId==0) {
                    continue;
                }
                $this->_storeRoots[$sId] = $rId;
            }
        }
        if (is_null($storeId)) {
            return $this->_storeRoots;
        }

        return isset($this->_storeRoots[$storeId]) ? $this->_storeRoots[$storeId] : null;
    }

    public function getRewrite($storeId, $idPath=null)
    {
        if (is_null($idPath)) {
            return isset($this->_rewrites[$storeId]) ? $this->_rewrites[$storeId] : null;
        }
        return isset($this->_rewrites[$storeId][$idPath]) ? $this->_rewrites[$storeId][$idPath] : null;
    }

    public function getCategory($storeId, $categoryId=null)
    {
        if (is_null($categoryId)) {
            return isset($this->_categories[$storeId]) ? $this->_categories[$storeId] : null;
        }
        return isset($this->_categories[$storeId][$categoryId]) ? $this->_categories[$storeId][$categoryId] : null;
    }

    public function getProduct($storeId, $productId=null)
    {
        if (is_null($productId)) {
            return $this->_products[$storeId];
        }
        return isset($this->_products[$storeId][$productId]) ? $this->_products[$storeId][$productId] : null;
    }

    public function refreshRewrites($storeId=null, $parentId=null)
    {
        if (is_null($storeId)) {
            foreach ($this->getRootId() as $storeId=>$rootId) {
                $this->loadRewrites($storeId);
                $this->loadCategories($storeId);
                $this->loadProducts($storeId);
                $this->refreshRewrites($storeId);
            }
            return $this;
        }

        if (is_null($parentId)) {

            $products = $this->getProduct($storeId);
            foreach ($products as $productId=>$product) {
                if (''==$product->getUrlKey()) {
                    continue;
                }
                $idPath = 'product/'.$productId;
                $productPath = $product->getUrlKey().'.html';
                $update = false;
                $rewrite = $this->getRewrite($storeId, $idPath);
                if ($rewrite) {
                    $update = $rewrite->getRequestPath() !== $productPath;
                } else {
                    $rewrite = Mage::getModel('core/url_rewrite')
                        ->setStoreId($storeId)
                        ->setIdPath($idPath)
                        ->setTargetPath('catalog/product/view/id/'.$productId);
                    $update = true;
                }
                if ($update) {
                    $rewrite->setRequestPath($productPath)->save();
                }
            }

            $parent = $this->getCategory($storeId, $this->getRootId($storeId));
            $parentPath = '';
        } else {
            $parent = $this->getCategory($storeId, $parentId);
            $parentPath = $parent->getUrlPath().'/';
        }
        if (!$parent) {
            return;
        }

        $categories = $parent->getChildren();
        if (is_array($categories)) {
            foreach ($categories as $categoryId=>$category) {
                if (''==$category->getUrlKey()) {
                    continue;
                }
                $idPath = 'category/'.$categoryId;
                $categoryPath = $parentPath.$category->getUrlKey();
                $update = false;
                $rewrite = $this->getRewrite($storeId, $idPath);
                if ($rewrite) {
                    $update = $rewrite->getRequestPath() !== $categoryPath;
                } else {
                    $rewrite = Mage::getModel('core/url_rewrite')
                        ->setStoreId($storeId)
                        ->setIdPath($idPath)
                        ->setTargetPath('catalog/category/view/id/'.$categoryId);
                    $update = true;
                }
                if ($update) {
                    $category->setUrlPath($categoryPath)->save();
                    $rewrite->setRequestPath($categoryPath)->save();
                }

                $products = $category->getProducts();
                if ($products) {
                    foreach ($products as $productId=>$product) {
                        if (''==$product->getUrlKey()) {
                            continue;
                        }
                        $idPath = 'product/'.$productId.'/'.$categoryId;
                        $productPath = $categoryPath.'/'.$product->getUrlKey().'.html';
                        $update = false;
                        $rewrite = $this->getRewrite($storeId, $idPath);
                        if ($rewrite) {
                            $update = $rewrite->getRequestPath() !== $productPath;
                        } else {
                            $rewrite = Mage::getModel('core/url_rewrite')
                                ->setStoreId($storeId)
                                ->setIdPath($idPath)
                                ->setTargetPath('catalog/product/view/id/'.$productId.'/category/'.$categoryId);
                            $update = true;
                        }
                        if ($update) {
                            $rewrite->setRequestPath($productPath)->save();
                        }
                    }
                }
                $this->refreshRewrites($storeId, $categoryId);
            }
        }

        return $this;
    }
}