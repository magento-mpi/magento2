<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\FullPageCache\Model\Processor;

class Category extends \Magento\FullPageCache\Model\Processor\DefaultProcessor
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
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $_catalogSession;

    /**
     * @param \Magento\FullPageCache\Model\Processor $fpcProcessor
     * @param \Magento\Core\Model\Session $coreSession
     * @param \Magento\App\State $appState
     * @param \Magento\FullPageCache\Model\Container\PlaceholderFactory $placeholderFactory
     * @param \Magento\FullPageCache\Model\ContainerFactory $containerFactory
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     */
    public function __construct(
        \Magento\FullPageCache\Model\Processor $fpcProcessor,
        \Magento\Core\Model\Session $coreSession,
        \Magento\App\State $appState,
        \Magento\FullPageCache\Model\Container\PlaceholderFactory $placeholderFactory,
        \Magento\FullPageCache\Model\ContainerFactory $containerFactory,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
    ) {
        parent::__construct($fpcProcessor, $coreSession, $appState, $placeholderFactory, $containerFactory);
        $this->_catalogSession = $catalogSession;
        $this->_catalogData = $catalogData;
        $this->_storeConfig = $coreStoreConfig;
    }

    /**
     * Return cache page id with application. Depends on catalog session and GET super global array.
     *
     * @param \Magento\FullPageCache\Model\Processor $processor
     * @return string
     */
    public function getPageIdInApp(\Magento\FullPageCache\Model\Processor $processor)
    {
        $queryParams = $this->_getQueryParams();

        \Magento\FullPageCache\Model\Cookie::setCategoryCookieValue($queryParams);
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
     * @param \Magento\FullPageCache\Model\Processor $processor
     * @return string
     */
    public function getPageIdWithoutApp(\Magento\FullPageCache\Model\Processor $processor)
    {
        $this->_updateCategoryViewedCookie($processor);
        $queryParams = $_GET;

        $sessionParams = \Magento\FullPageCache\Model\Cookie::getCategoryCookieValue();
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

        \Magento\FullPageCache\Model\Cookie::setCategoryCookieValue($queryParams);

        return $processor->getRequestId() . '_' . md5($queryParams);
    }

    /**
     * Check if request can be cached
     * @param \Magento\App\RequestInterface $request
     * @return bool
     */
    public function allowCache(\Magento\App\RequestInterface $request)
    {
        $res = parent::allowCache($request);
        if ($res) {
            $params = $this->_getSessionParams();
            $queryParams = $request->getQuery();
            $queryParams = array_merge($queryParams, $params);
            $maxDepth = $this->_storeConfig->getValue(\Magento\FullPageCache\Model\Processor::XML_PATH_ALLOWED_DEPTH, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
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
        $data   = $this->_catalogSession->getData();
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
     * @return void
     */
    protected function _prepareCatalogSession()
    {
        $queryParams = json_decode($this->_getQueryParams(), true);
        if (empty($queryParams)) {
            $queryParams = \Magento\FullPageCache\Model\Cookie::getCategoryCookieValue();
            $queryParams = json_decode($queryParams, true);
        }

        if (is_array($queryParams) && !empty($queryParams)) {
            $flipParamsMap = array_flip($this->_paramsMap);
            foreach ($queryParams as $key => $value) {
                if (in_array($key, $this->_paramsMap)) {
                    $this->_catalogSession->setData($flipParamsMap[$key], $value);
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
     * @param \Magento\FullPageCache\Model\Processor $processor
     * @return $this
     */
    protected function _updateCategoryViewedCookie(\Magento\FullPageCache\Model\Processor $processor)
    {
        \Magento\FullPageCache\Model\Cookie::setCategoryViewedCookieValue(
            $processor->getMetadata(self::METADATA_CATEGORY_ID)
        );
        return $this;
    }
}
