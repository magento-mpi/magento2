<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_FullPageCache_Model_Processor_Category extends Magento_FullPageCache_Model_Processor_Default
{
    /**
     * Key for saving category id in metadata
     */
    const METADATA_CATEGORY_ID = 'catalog_category_id';

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
     * Return cache page id with application. Depends on catalog session and GET super global array.
     *
     * @param Magento_FullPageCache_Model_Processor $processor
     * @return string
     */
    public function getPageIdInApp(Magento_FullPageCache_Model_Processor $processor)
    {
        $queryParams = $this->_getQueryParams();

        Magento_FullPageCache_Model_Cookie::setCategoryCookieValue($queryParams);
        $this->_prepareCatalogSession();

        $category = Mage::helper('Magento_Catalog_Helper_Data')->getCategory();
        if ($category) {
            $processor->setMetadata(self::METADATA_CATEGORY_ID, $category->getId());
            $this->_updateCategoryViewedCookie($processor);
        }

        return $processor->getRequestId() . '_' . md5($queryParams);
    }

    /**
     * Return cache page id without application. Depends on GET super global array.
     *
     * @param Magento_FullPageCache_Model_Processor $processor
     * @return string
     */
    public function getPageIdWithoutApp(Magento_FullPageCache_Model_Processor $processor)
    {
        $this->_updateCategoryViewedCookie($processor);
        $queryParams = $_GET;

        $sessionParams = Magento_FullPageCache_Model_Cookie::getCategoryCookieValue();
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

        Magento_FullPageCache_Model_Cookie::setCategoryCookieValue($queryParams);

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
            $maxDepth = Mage::getStoreConfig(Magento_FullPageCache_Model_Processor::XML_PATH_ALLOWED_DEPTH);
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
     *
     * @param string $queryParams
     */
    protected function _prepareCatalogSession()
    {
        $queryParams = json_decode($this->_getQueryParams(), true);
        if (empty($queryParams)) {
            $queryParams = Magento_FullPageCache_Model_Cookie::getCategoryCookieValue();
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
     * @param Magento_FullPageCache_Model_Processor $processor
     * @return Magento_FullPageCache_Model_Processor_Category
     */
    protected function _updateCategoryViewedCookie(Magento_FullPageCache_Model_Processor $processor)
    {
        Magento_FullPageCache_Model_Cookie::setCategoryViewedCookieValue(
            $processor->getMetadata(self::METADATA_CATEGORY_ID)
        );
        return $this;
    }
}
