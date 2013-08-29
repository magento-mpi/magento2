<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_PageCache_Model_Processor_Category extends Enterprise_PageCache_Model_Processor_Default
{
    /**
     * Key for saving category id in metadata
     */
    const METADATA_CATEGORY_ID = 'catalog_category_id';

    /**
     * Map of parameters
     *
     * @var array
     */
    protected $_paramsMap = array(
        'display_mode'  => 'mode',
        'limit_page'    => 'limit',
        'sort_order'    => 'order',
        'sort_direction'=> 'dir',
    );

    /**
     * Query params
     *
     * @var string
     */
    protected $_queryParams;

    /**
     * Catalog data
     *
     * @var Magento_Catalog_Helper_Data
     */
    protected $_catalogData = null;

    /**
     * @param Magento_Catalog_Helper_Data $catalogData
     */
    public function __construct(
        Magento_Catalog_Helper_Data $catalogData
    ) {
        $this->_catalogData = $catalogData;
    }

    /**
     * Return cache page id with application. Depends on catalog session and GET super global array.
     *
     * @param Enterprise_PageCache_Model_Processor $processor
     * @return string
     */
    public function getPageIdInApp(Enterprise_PageCache_Model_Processor $processor)
    {
        $queryParams = $this->_getQueryParams();

        Enterprise_PageCache_Model_Cookie::setCategoryCookieValue($queryParams);
        $this->_prepareCatalogSession();

        $category = $this->_catalogData->getCategory();
        if ($category) {
            $processor->setMetadata(self::METADATA_CATEGORY_ID, $category->getId());
            $this->_updateCategoryViewedCookie($processor);
        }

        return $processor->getRequestId() . '_' . md5($queryParams);
    }

    /**
     * Return cache page id without application. Depends on GET super global array.
     *
     * @param Enterprise_PageCache_Model_Processor $processor
     * @return string
     */
    public function getPageIdWithoutApp(Enterprise_PageCache_Model_Processor $processor)
    {
        $this->_updateCategoryViewedCookie($processor);
        $queryParams = $_GET;

        $sessionParams = Enterprise_PageCache_Model_Cookie::getCategoryCookieValue();
        if ($sessionParams) {
            $sessionParams = (array)json_decode($sessionParams);
            foreach ($sessionParams as $key => $value) {
                if (in_array($key, $this->_paramsMap) && !isset($queryParams[$key])) {
                    $queryParams[$key] = $value;
                }
            }
        }
        ksort($queryParams);
        $queryParams = json_encode($queryParams);

        Enterprise_PageCache_Model_Cookie::setCategoryCookieValue($queryParams);

        return $processor->getRequestId() . '_' . md5($queryParams);
    }

    /**
     * Check if request can be cached
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function allowCache(Zend_Controller_Request_Http $request)
    {
        $res = parent::allowCache($request);
        if ($res) {
            $params = $this->_getSessionParams();
            $queryParams = $request->getQuery();
            $queryParams = array_merge($queryParams, $params);
            $maxDepth = Mage::getStoreConfig(Enterprise_PageCache_Model_Processor::XML_PATH_ALLOWED_DEPTH);
            $res = count($queryParams)<=$maxDepth;
        }
        return $res;
    }

    /**
     * Get page view related parameters from session mapped to wuery parametes
     * @return array
     */
    protected function _getSessionParams()
    {
        $params = array();
        $data   = Mage::getSingleton('Magento_Catalog_Model_Session')->getData();
        foreach ($this->_paramsMap as $sessionParam => $queryParam) {
            if (isset($data[$sessionParam])) {
                $params[$queryParam] = $data[$sessionParam];
            }
        }
        return $params;
    }

    /**
     * Update catalog session from GET or cookies
     */
    protected function _prepareCatalogSession()
    {
        $queryParams = json_decode($this->_getQueryParams(), true);
        if (empty($queryParams)) {
            $queryParams = Enterprise_PageCache_Model_Cookie::getCategoryCookieValue();
            $queryParams = json_decode($queryParams, true);
        }

        if (is_array($queryParams) && !empty($queryParams)) {
            $session = Mage::getSingleton('Magento_Catalog_Model_Session');
            $flipParamsMap = array_flip($this->_paramsMap);
            foreach ($queryParams as $key => $value) {
                if (in_array($key, $this->_paramsMap)) {
                    $session->setData($flipParamsMap[$key], $value);
                }
            }
        }
    }

    /**
     * Return merged session and GET params
     *
     * @return string
     */
    protected function _getQueryParams()
    {
        if (is_null($this->_queryParams)) {
            $queryParams = array_merge($this->_getSessionParams(), $_GET);
            ksort($queryParams);
            $this->_queryParams = json_encode($queryParams);
        }

        return $this->_queryParams;
    }

    /**
     * Update last visited category id cookie
     *
     * @param Enterprise_PageCache_Model_Processor $processor
     * @return Enterprise_PageCache_Model_Processor_Category
     */
    protected function _updateCategoryViewedCookie(Enterprise_PageCache_Model_Processor $processor)
    {
        Enterprise_PageCache_Model_Cookie::setCategoryViewedCookieValue(
            $processor->getMetadata(self::METADATA_CATEGORY_ID)
        );
        return $this;
    }
}
