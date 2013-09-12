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
 * Catalog category helper
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Helper_Category extends Magento_Core_Helper_Abstract
{
    const XML_PATH_CATEGORY_URL_SUFFIX          = 'catalog/seo/category_url_suffix';
    const XML_PATH_USE_CATEGORY_CANONICAL_TAG   = 'catalog/seo/category_canonical_tag';
    const XML_PATH_CATEGORY_ROOT_ID             = 'catalog/category/root_id';

    /**
     * Store categories cache
     *
     * @var array
     */
    protected $_storeCategories = array();

    /**
     * Cache for category rewrite suffix
     *
     * @var array
     */
    protected $_categoryUrlSuffix = array();

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Retrieve current store categories
     *
     * @param   boolean|string $sorted
     * @param   boolean $asCollection
     * @return  Magento_Data_Tree_Node_Collection|Magento_Catalog_Model_Resource_Category_Collection|array
     */
    public function getStoreCategories($sorted=false, $asCollection=false, $toLoad=true)
    {
        $parent     = Mage::app()->getStore()->getRootCategoryId();
        $cacheKey   = sprintf('%d-%d-%d-%d', $parent, $sorted, $asCollection, $toLoad);
        if (isset($this->_storeCategories[$cacheKey])) {
            return $this->_storeCategories[$cacheKey];
        }

        /**
         * Check if parent node of the store still exists
         */
        $category = Mage::getModel('Magento_Catalog_Model_Category');
        /* @var $category Magento_Catalog_Model_Category */
        if (!$category->checkId($parent)) {
            if ($asCollection) {
                return new Magento_Data_Collection();
            }
            return array();
        }

        $recursionLevel  = max(0, (int) Mage::app()->getStore()->getConfig('catalog/navigation/max_depth'));
        $storeCategories = $category->getCategories($parent, $recursionLevel, $sorted, $asCollection, $toLoad);

        $this->_storeCategories[$cacheKey] = $storeCategories;
        return $storeCategories;
    }

    /**
     * Retrieve category url
     *
     * @param   Magento_Catalog_Model_Category $category
     * @return  string
     */
    public function getCategoryUrl($category)
    {
        if ($category instanceof Magento_Catalog_Model_Category) {
            return $category->getUrl();
        }
        return Mage::getModel('Magento_Catalog_Model_Category')
            ->setData($category->getData())
            ->getUrl();
    }

    /**
     * Check if a category can be shown
     *
     * @param  Magento_Catalog_Model_Category|int $category
     * @return boolean
     */
    public function canShow($category)
    {
        if (is_int($category)) {
            $category = Mage::getModel('Magento_Catalog_Model_Category')->load($category);
        }

        if (!$category->getId()) {
            return false;
        }

        if (!$category->getIsActive()) {
            return false;
        }
        if (!$category->isInRootCategoryList()) {
            return false;
        }

        return true;
    }

/**
     * Retrieve category rewrite sufix for store
     *
     * @param int $storeId
     * @return string
     */
    public function getCategoryUrlSuffix($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }

        if (!isset($this->_categoryUrlSuffix[$storeId])) {
            $this->_categoryUrlSuffix[$storeId] = $this->_coreStoreConfig->getConfig(
                self::XML_PATH_CATEGORY_URL_SUFFIX, $storeId
            );
        }
        return $this->_categoryUrlSuffix[$storeId];
    }

    /**
     * Retrieve clear url for category as parrent
     *
     * @param string $url
     * @param bool $slash
     * @param int $storeId
     *
     * @return string
     */
    public function getCategoryUrlPath($urlPath, $slash = false, $storeId = null)
    {
        if (!$this->getCategoryUrlSuffix($storeId)) {
            return $urlPath;
        }

        if ($slash) {
            $regexp     = '#('.preg_quote($this->getCategoryUrlSuffix($storeId), '#').')/$#i';
            $replace    = '/';
        }
        else {
            $regexp     = '#('.preg_quote($this->getCategoryUrlSuffix($storeId), '#').')$#i';
            $replace    = '';
        }

        return preg_replace($regexp, $replace, $urlPath);
    }

    /**
     * Check if <link rel="canonical"> can be used for category
     *
     * @param $store
     * @return bool
     */
    public function canUseCanonicalTag($store = null)
    {
        return $this->_coreStoreConfig->getConfig(self::XML_PATH_USE_CATEGORY_CANONICAL_TAG, $store);
    }
}
