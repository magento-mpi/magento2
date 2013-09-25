<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product Url model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Url extends Magento_Object
{
    const CACHE_TAG = 'url_rewrite';

    /**
     * Static URL instance
     *
     * @var Magento_Core_Model_Url
     */
    protected $_url;

    /**
     * Static URL Rewrite Instance
     *
     * @var Magento_Core_Model_Url_Rewrite
     */
    protected $_urlRewrite;

    /**
     * Catalog product url
     *
     * @var Magento_Catalog_Helper_Product_Url
     */
    protected $_catalogProductUrl = null;

    /**
     * Catalog category
     *
     * @var Magento_Catalog_Helper_Category
     */
    protected $_catalogCategory = null;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * App model
     *
     * @var Magento_Core_Model_App
     */
    protected $_app;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Url_RewriteFactory $urlRewriteFactory
     * @param Magento_Core_Model_UrlInterface $url
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Helper_Category $catalogCategory
     * @param Magento_Catalog_Helper_Product_Url $catalogProductUrl
     * @param Magento_Core_Model_App $app
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Url_RewriteFactory $urlRewriteFactory,
        Magento_Core_Model_UrlInterface $url,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Helper_Category $catalogCategory,
        Magento_Catalog_Helper_Product_Url $catalogProductUrl,
        Magento_Core_Model_App $app,
        array $data = array()
    ) {
        $this->_urlRewrite = $urlRewriteFactory->create();
        $this->_url = $url;
        $this->_storeManager = $storeManager;
        $this->_catalogCategory = $catalogCategory;
        $this->_catalogProductUrl = $catalogProductUrl;
        $this->_app = $app;
        parent::__construct($data);
    }

    /**
     * Retrieve URL Instance
     *
     * @return Magento_Core_Model_Url
     */
    public function getUrlInstance()
    {
        return $this->_url;
    }

    /**
     * Retrieve URL Rewrite Instance
     *
     * @return Magento_Core_Model_Url_Rewrite
     */
    public function getUrlRewrite()
    {
        return $this->_urlRewrite;
    }

    /**
     * 'no_selection' shouldn't be a valid image attribute value
     *
     * @param string $image
     * @return string
     */
    protected function _validImage($image)
    {
        if($image == 'no_selection') {
            $image = null;
        }
        return $image;
    }

    /**
     * Retrieve URL in current store
     *
     * @param Magento_Catalog_Model_Product $product
     * @param array $params the URL route params
     * @return string
     */
    public function getUrlInStore(Magento_Catalog_Model_Product $product, $params = array())
    {
        $params['_store_to_url'] = true;
        return $this->getUrl($product, $params);
    }

    /**
     * Retrieve Product URL
     *
     * @param  Magento_Catalog_Model_Product $product
     * @param  bool $useSid forced SID mode
     * @return string
     */
    public function getProductUrl($product, $useSid = null)
    {
        if ($useSid === null) {
            $useSid = $this->_app->getUseSessionInUrl();
        }

        $params = array();
        if (!$useSid) {
            $params['_nosid'] = true;
        }

        return $this->getUrl($product, $params);
    }

    /**
     * Format Key for URL
     *
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', $this->_catalogProductUrl->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
    }

    /**
     * Retrieve Product Url path (with category if exists)
     *
     * @param Magento_Catalog_Model_Product $product
     * @param Magento_Catalog_Model_Category $category
     *
     * @return string
     * @throws Magento_Core_Exception
     */
    public function getUrlPath($product, $category=null)
    {
        $path = $product->getData('url_path');

        if (is_null($category)) {
            /** @todo get default category */
            return $path;
        } elseif (!$category instanceof Magento_Catalog_Model_Category) {
            throw new Magento_Core_Exception('Invalid category object supplied');
        }

        return $this->_catalogCategory->getCategoryUrlPath($category->getUrlPath())
            . '/' . $path;
    }

    /**
     * Retrieve Product URL using UrlDataObject
     *
     * @param Magento_Catalog_Model_Product $product
     * @param array $params
     * @return string
     */
    public function getUrl(Magento_Catalog_Model_Product $product, $params = array())
    {
        $routePath      = '';
        $routeParams    = $params;

        $storeId    = $product->getStoreId();
        if (isset($params['_ignore_category'])) {
            unset($params['_ignore_category']);
            $categoryId = null;
        } else {
            $categoryId = $product->getCategoryId() && !$product->getDoNotUseCategoryId()
                ? $product->getCategoryId() : null;
        }

        if ($product->hasUrlDataObject()) {
            $requestPath = $product->getUrlDataObject()->getUrlRewrite();
            $routeParams['_store'] = $product->getUrlDataObject()->getStoreId();
        } else {
            $requestPath = $product->getRequestPath();
            if (empty($requestPath) && $requestPath !== false) {
                $idPath = sprintf('product/%d', $product->getEntityId());
                if ($categoryId) {
                    $idPath = sprintf('%s/%d', $idPath, $categoryId);
                }
                $rewrite = $this->getUrlRewrite();
                $rewrite->setStoreId($storeId)
                    ->loadByIdPath($idPath);
                if ($rewrite->getId()) {
                    $requestPath = $rewrite->getRequestPath();
                    $product->setRequestPath($requestPath);
                } else {
                    $product->setRequestPath(false);
                }
            }
        }

        if (isset($routeParams['_store'])) {
            $storeId = $this->_storeManager->getStore($routeParams['_store'])->getId();
        }

        if ($storeId != $this->_storeManager->getStore()->getId()) {
            $routeParams['_store_to_url'] = true;
        }

        if (!empty($requestPath)) {
            $routeParams['_direct'] = $requestPath;
        } else {
            $routePath = 'catalog/product/view';
            $routeParams['id']  = $product->getId();
            $routeParams['s']   = $product->getUrlKey();
            if ($categoryId) {
                $routeParams['category'] = $categoryId;
            }
        }

        // reset cached URL instance GET query params
        if (!isset($routeParams['_query'])) {
            $routeParams['_query'] = array();
        }

        return $this->getUrlInstance()->setStore($storeId)
            ->getUrl($routePath, $routeParams);
    }
}
