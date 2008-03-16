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
 * @author     Victor Tihonchuk <victor@varien.com>
 */
class Mage_Catalog_Model_Url
{
    /**
     * Stores configuration array
     *
     * @var array
     */
    protected $_stores;

    /**
     * Resource model
     *
     * @var Mage_Catalog_Model_Resource_Eav_Mysql4_Url
     */
    protected $_resourceModel;

    /**
     * Categories cache for products
     *
     * @var array
     */
    protected $_categories = array();

    /**
     * Retrieve stores array or store model
     *
     * @param int $storeId
     * @return Mage_Core_Model_Store|array
     */
    public function getStores($storeId = null)
    {
    	if (is_null($this->_stores)) {
    	    $this->_stores = Mage::app()->getStores();
    	}
    	if ($storeId && isset($this->_stores[$storeId])) {
    	    return $this->_stores[$storeId];
    	}
    	return $this->_stores;
    }

    /**
     * Retrieve resource model
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Url
     */
    public function getResource()
    {
        if (is_null($this->_resourceModel)) {
            $this->_resourceModel = Mage::getResourceModel('catalog/url');
        }
        return $this->_resourceModel;
    }

    /**
     * Retrieve Category model singleton
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCategoryModel()
    {
        return Mage::getSingleton('catalog/category');
    }

    /**
     * Retrieve product model singleton
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProductModel()
    {
        return Mage::getSingleton('catalog/product');
    }

    /**
     * Refresh rewrite urls
     *
     * @param int $storeId
     * @return Mage_Catalog_Model_Url
     */
    public function refreshRewrites($storeId = null)
    {
        if (is_null($storeId)) {
            foreach ($this->getStores() as $store) {
                $this->refreshRewrites($store->getId());
            }
            return $this;
        }

        $this->refreshCategoryRewrite($this->getStores($storeId)->getRootCategoryId(), $storeId, false);
        $this->refreshProductRewrites($storeId);
    }

    /**
     * Refresh category rewrite
     *
     * @param Varien_Object $category
     * @param string $parentPath
     * @return Mage_Catalog_Model_Url
     */
    protected function _refreshCategoryRewrites(Varien_Object $category, $parentPath = null, $refreshProducts = true)
    {
        if ($category->getUrlKey == '') {
            $urlKey = $this->getCategoryModel()->formatUrlKey($category->getName());
        }

        if ($category->getId() != $this->getStores($category->getStoreId())->getRootCategoryId()) {
            if ($category->getUrlKey() == '') {
                $urlKey = $this->getCategoryModel()->formatUrlKey($category->getName());
            }
            else {
                $urlKey = $this->getCategoryModel()->formatUrlKey($category->getUrlKey());
            }

            if (is_null($parentPath)) {
                $parentPath = $this->getResource()->getCategoryParentPath($category);
            } elseif ($parentPath == '/') {
                $parentPath = '';
            }

            $idPath      = 'category/' . $category->getId();
            $targetPath  = 'catalog/category/view/id/'.$category->getId();
            $requestPath = $this->getUnusedPath($category->getStoreId(), $parentPath . $urlKey, $idPath);

            $rewrite = Mage::getSingleton('core/url_rewrite');
            /* @var $rewrite Mage_Core_Model_Url_Rewrite */
            $rewrite->setId(null)->setStoreId($category->getStoreId())->loadByIdPath($idPath);
            if (!$rewrite->getId() || $targetPath != $rewrite->getTargetPath()
                || $requestPath != $rewrite->getRequestPath() || $category->getId() != $rewrite->getEntityId()
                || Mage_Core_Model_Url_Rewrite::TYPE_CATEGORY != $rewrite->getType()) {
                $rewrite->setStoreId($category->getStoreId())
                    ->setEntityId($category->getId())
                    ->setIdPath($idPath)
                    ->setRequestPath($requestPath)
                    ->setTargetPath($targetPath)
                    ->setType(Mage_Core_Model_Url_Rewrite::TYPE_CATEGORY)
                    ->save();
            }

            if ($category->getUrlKey() != $urlKey) {
                $category->setUrlKey($urlKey);
                $this->getResource()->saveCategoryAttribute($category, 'url_key');
            }
            if ($category->getUrlPath() != $requestPath) {
                $category->setUrlPath($requestPath);
                $this->getResource()->saveCategoryAttribute($category, 'url_path');
            }
        }
        else {
            $category->setUrlPath('');
        }

        if ($refreshProducts) {
            $this->_refreshCategoryProductRewrites($category);
        }

        foreach ($category->getChilds() as $child) {
            $this->_refreshCategoryRewrites($child, $category->getUrlPath() . '/');
        }

        return $this;
    }

    /**
     * Refresh product rewrite
     *
     * @param Varien_Object $product
     * @param Varien_Object $category
     * @return Mage_Catalog_Model_Url
     */
    protected function _refreshProductRewrite(Varien_Object $product, Varien_Object $category)
    {
        if ($category->getId() == $category->getPath()) {
            return $this;
        }
        if ($product->getUrlKey() == '') {
            $urlKey = $this->getProductModel()->formatUrlKey($product->getName());
        }
        else {
            $urlKey = $this->getProductModel()->formatUrlKey($product->getUrlKey());
        }

        $productUrlSuffix = (string)Mage::app()->getStore($category->getStoreId())->getConfig('catalog/seo/product_url_suffix');
        if ($category->getUrlPath()) {
            $idPath = 'product/'.$product->getId().'/'.$category->getId();
            $targetPath = 'catalog/product/view/id/'.$product->getId().'/category/'.$category->getId();
            $requestPath = $this->getUnusedPath($category->getStoreId(), $category->getUrlPath() . '/' . $urlKey . $productUrlSuffix, $idPath);
            $updateKeys = false;
        }
        else {
            $idPath = 'product/'.$product->getId();
            $targetPath = 'catalog/product/view/id/'.$product->getId();
            $requestPath = $this->getUnusedPath($category->getStoreId(), $urlKey . $productUrlSuffix, $idPath);
            $updateKeys = true;
        }

        $rewrite = Mage::getSingleton('core/url_rewrite');
        /* @var $rewrite Mage_Core_Model_Url_Rewrite */
        $rewrite->setId(null)->setStoreId($category->getStoreId())->loadByIdPath($idPath);
        if (!$rewrite->getId() || $targetPath != $rewrite->getTargetPath()
            || $requestPath != $rewrite->getRequestPath() || $category->getId() != $rewrite->getEntityId()
            || Mage_Core_Model_Url_Rewrite::TYPE_CATEGORY != $rewrite->getType()) {
            $rewrite->setStoreId($category->getStoreId())
                ->setEntityId($category->getId())
                ->setIdPath($idPath)
                ->setRequestPath($requestPath)
                ->setTargetPath($targetPath)
                ->setType(Mage_Core_Model_Url_Rewrite::TYPE_PRODUCT)
                ->save();
        }

        if ($updateKeys && $product->getUrlKey() != $urlKey) {
            $product->setUrlKey($urlKey);
            $this->getResource()->saveProductAttribute($product, 'url_key');
        }
        if ($updateKeys && $product->getUrlPath() != $requestPath) {
            $product->setUrlPath($requestPath);
            $this->getResource()->saveProductAttribute($product, 'url_path');
        }

        return $this;
    }

    /**
     * Refresh products for catwgory
     *
     * @param Varien_Object $category
     * @return Mage_Catalog_Model_Url
     */
    protected function _refreshCategoryProductRewrites(Varien_Object $category)
    {
        $process = true;
        $lastEntityId = 0;
        while ($process == true) {
            $products = $this->getResource()->getProductsByCategory($category, $lastEntityId);
            if (!$products) {
                $process = false;
                break;
            }

            foreach ($products as $product) {
                $this->_refreshProductRewrite($product, $category);
            }
            unset($products);
        }
        return $this;
    }

    /**
     * Refresh category and childs rewrites
     *
     * @param int $categoryId
     * @param int $storeId
     * @param bool $refreshProducts
     * @return Mage_Catalog_Model_Url
     */
    public function refreshCategoryRewrite($categoryId, $storeId = null, $refreshProducts = true)
    {
        if (is_null($storeId)) {
            foreach ($this->getStores() as $store) {
                $this->refreshCategoryRewrite($categoryId, $store->getId(), $refreshProducts);
            }
            return $this;
    	}

    	$category = $this->getResource()->getCategory($categoryId, $storeId);
    	$category = $this->getResource()->loadCategoryChilds($category);

    	$this->_refreshCategoryRewrites($category, null, $refreshProducts);

    	unset($category);
        return $this;
    }

    /**
     * Refresh product and categories urls
     *
     * @param int $productId
     * @param int $storeId
     * @return Mage_Catalog_Model_Url
     */
    public function refreshProductRewrite($productId, $storeId = null)
    {
        if (is_null($storeId)) {
            foreach ($this->getStores() as $store) {
                $this->refreshProductRewrite($productId, $store->getId());
            }
            return $this;
    	}
        $product = $this->getResource()->getProduct($productId);

        $storeRootCategoryId = $this->getStores($storeId)->getRootCategoryId();
        $categories = $this->getResource()->getCategories($product->getCategoryIds(), $storeId);

        if (!isset($categories[$storeRootCategoryId])) {
            $categories[$storeRootCategoryId] = $this->getResource()->getCategory($storeRootCategoryId, $storeId);
        }

        foreach ($categories as $category) {
            $this->_refreshProductRewrite($product, $category);
        }

        unset($categories);
        unset($product);

        return $this;
    }

    public function refreshProductRewrites($storeId)
    {
        $this->_categories = array();
        $storeRootCategoryId = $this->getStores($storeId)->getRootCategoryId();
        $this->_categories[$storeRootCategoryId] = $this->getResource()->getCategory($storeRootCategoryId, $storeId);

        $lastEntityId = 0;
        $process = true;

        while ($process == true) {
            $products = $this->getResource()->getProductsByStore($storeId, $lastEntityId);
            if (!$products) {
                $process = false;
                break;
            }

            $loadCategories = array();
            foreach ($products as $product) {
                foreach ($product->getCategoryIds() as $categoryId) {
                    if (!isset($this->_categories[$categoryId])) {
                        $loadCategories[$categoryId] = $categoryId;
                    }
                }
            }

            if ($loadCategories) {
                foreach ($this->getResource()->getCategories($loadCategories, $storeId) as $category) {
                    $this->_categories[$category->getId()] = $category;
                }
            }

            foreach ($products as $product) {
                $this->_refreshProductRewrite($product, $this->_categories[$storeRootCategoryId]);
                foreach ($product->getCategoryIds() as $categoryId) {
                    if ($categoryId != $storeRootCategoryId && isset($this->_categories[$categoryId])) {
                        $this->_refreshProductRewrite($product, $this->_categories[$categoryId]);
                    }
                }
            }
            unset($products);
        }

        $this->_categories = array();
        return $this;
    }

    /**
     * Get requestPath that was not used yet.
     *
     * Will try to get unique path by adding -1 -2 etc. between url_key and optional url_suffix
     *
     * @param int $storeId
     * @param string $requestPath
     * @param string $idPath
     * @return string
     */
    public function getUnusedPath($storeId, $requestPath, $idPath)
    {
        $rewrite = $this->getResource()->getRewriteByIdPath($idPath, $storeId);
        if ($rewrite && $rewrite->getRequestPath() == $requestPath) {
            unset($rewrite);
            return $requestPath;
        }
        $rewrite = $this->getResource()->getRewriteByRequestPath($requestPath, $storeId);
        if ($rewrite && $rewrite->getId()) {
            // retrieve url_suffix for product urls
            $productUrlSuffix = (string)Mage::app()->getStore($storeId)->getConfig('catalog/seo/product_url_suffix');
            // match request_url abcdef1234(-12)(.html) pattern
            $match = array();
            if (!preg_match('#^([0-9a-z/-]+?)(-([0-9]+))?('.preg_quote($productUrlSuffix).')?$#i', $requestPath, $match)) {
                unset($rewrite);
                return $this->getUnusedPath($storeId, '-', $idPath);
            }
            $requestPath = $match[1].(isset($match[3])?'-'.($match[3]+1):'-1').(isset($match[4])?$match[4]:'');
            unset($rewrite);
            return $this->getUnusedPath($storeId, $requestPath, $idPath);
        }
        else {
            return $requestPath;
        }
    }

}