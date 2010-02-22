<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_PageCache_Model_Processor
{
    const NO_CACHE_COOKIE               = 'NO_CACHE';
    const LAST_PRODUCT_COOKIE           = 'LAST_PRODUCT';
    const XML_NODE_ALLOWED_CACHE        = 'frontend/cache/requests';
    const XML_PATH_ALLOWED_DEPTH        = 'system/page_cache/allowed_depth';
    const XML_PATH_LIFE_TIME            = 'system/page_cache/lifetime';
    const XML_PATH_CACHE_MULTICURRENCY  = 'system/page_cache/multicurrency';
    const REQUEST_ID_PREFIX             = 'REQEST_';
    const CACHE_TAG                     = 'FPC';  // Full Page Cache, minimize

    /**
     * Request identifier
     *
     * @var string
     */
    protected $_requestId;

    /**
     * Request page cache identifier
     *
     * @var string
     */
    protected $_requestCacheId;

    /**
     * Cache tags related with request
     * @var array
     */
    protected $_requestTags;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $uri = false;
        /**
         * Define server HTTP HOST
         */
        if (isset($_SERVER['HTTP_HOST'])) {
            $uri = $_SERVER['HTTP_HOST'];
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $uri = $_SERVER['SERVER_NAME'];
        }

        /**
         * Define request URI
         */
        if ($uri) {
            if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
                $uri.= $_SERVER['HTTP_X_REWRITE_URL'];
            } elseif (isset($_SERVER['REQUEST_URI'])) {
                $uri.= $_SERVER['REQUEST_URI'];
            } elseif (!empty($_SERVER['IIS_WasUrlRewritten']) && !empty($_SERVER['UNENCODED_URL'])) {
                $uri.= $_SERVER['UNENCODED_URL'];
            } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
                $uri.= $_SERVER['ORIG_PATH_INFO'];
                if (!empty($_SERVER['QUERY_STRING'])) {
                    $uri.= $_SERVER['QUERY_STRING'];
                }
            } else {
                $uri = false;
            }
        }

        /**
         * Define COOKIE state
         */
        if ($uri) {
            if (isset($_COOKIE['store'])) {
                $uri = $uri.'_'.$_COOKIE['store'];
            }
            if (isset($_COOKIE['currency'])) {
                $uri = $uri.'_'.$_COOKIE['currency'];
            }
        }
        $this->_requestId       = $uri;
        $this->_requestCacheId  = $this->prepareCacheId($this->_requestId);
        $this->_requestTags     = array(self::CACHE_TAG);
    }

    /**
     * Prepare page identifier
     *
     * @param string $id
     * @return string
     */
    public function prepareCacheId($id)
    {
        return self::REQUEST_ID_PREFIX.md5($id);
    }

    /**
     * Get HTTP request identifier
     *
     * @return string
     */
    public function getRequestId()
    {
        return $this->_requestId;
    }

    /**
     * Get page identifier for loading page from cache
     * @return string
     */
    public function getRequestCacheId()
    {
        return $this->_requestCacheId;
    }

    /**
     * Check if processor is allowed for current HTTP request.
     * Disable processing HTTPS requests and requests with "NO_CACHE" cookie
     *
     * @return bool
     */
    public function isAllowed()
    {
        if (!$this->_requestId) {
            return false;
        }
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            return false;
        }
        if (isset($_COOKIE['NO_CACHE'])) {
            return false;
        }
        if (isset($_GET['no_cache'])) {
            return false;
        }
        return true;
    }

    /**
     * Get page content from cache storage
     *
     * @param string $content
     * @return string | false
     */
    public function extractContent($content)
    {
        if (!$content && $this->isAllowed()) {
            $content = Mage::app()->loadCache($this->getRequestCacheId());
        }
        return $content;
    }

    /**
     * Associate tag with page cache request identifier
     *
     * @param array|string $tag
     * @return Enterprise_PageCache_Model_Processor
     */
    public function addRequestTag($tag)
    {
        if (is_array($tag)) {
            $this->_requestTags = array_merge($this->_requestTags, $tag);
        } else {
            $this->_requestTags[] = $tag;
        }
        return $this;
    }

    /**
     * Get cache request associated tags
     * @return array
     */
    public function getRequestTags()
    {
        return $this->_requestTags;
    }

    /**
     * Process response body by specific request
     *
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Controller_Response_Http $response
     * @return Enterprise_PageCache_Model_Processor
     */
    public function processRequestResponse(Zend_Controller_Request_Http $request, Zend_Controller_Response_Http $response)
    {
        /**
         * Basic validation for request processing
         */
        if ($this->canProcessRequest($request)) {
            $processor = $this->getRequestProcessor($request);
            if ($processor && $processor->allowCache($request)) {
                $cacheId = $this->prepareCacheId($processor->getRequestUri($this, $request));
                $content = $processor->prepareContent($response);
                $lifetime = Mage::getStoreConfig(self::XML_PATH_LIFE_TIME)*60;

                Mage::app()->saveCache($content, $cacheId, $this->getRequestTags(), $lifetime);
            }
        }
        return $this;
    }

    /**
     * Do basic validation for request to be cached
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function canProcessRequest(Zend_Controller_Request_Http $request)
    {
        $res = $this->isAllowed();
        $res = $res && Mage::app()->useCache('full_page');
        if ($request->getParam('no_cache')) {
            $res = false;
        }

        if ($res) {
            $maxDepth = Mage::getStoreConfig(self::XML_PATH_ALLOWED_DEPTH);
            $queryParams = $request->getQuery();
            $res = count($queryParams)<=$maxDepth;
        }
        if ($res) {
            $multicurrency = Mage::getStoreConfig(self::XML_PATH_CACHE_MULTICURRENCY);
            if (!$multicurrency && !empty($_COOKIE['currency'])) {
                $res = false;
            }
        }
        return $res;
    }

    /**
     * Get specific request processor based on request parameters.
     *
     * @param Zend_Controller_Request_Http $request
     * @return Enterprise_PageCache_Model_Processor_Default
     */
    public function getRequestProcessor(Zend_Controller_Request_Http $request)
    {
        $processor = false;
        $configuration = Mage::getConfig()->getNode(self::XML_NODE_ALLOWED_CACHE);
        if ($configuration) {
            $configuration = $configuration->asArray();
        }
        $module = $request->getModuleName();
        if (isset($configuration[$module])) {
            $model = $configuration[$module];
            $controller = $request->getControllerName();
            if (is_array($configuration[$module]) && isset($configuration[$module][$controller])) {
                $model = $configuration[$module][$controller];
                $action = $request->getActionName();
                if (is_array($configuration[$module][$controller]) && isset($configuration[$module][$controller][$action])) {
                    $model = $configuration[$module][$controller][$action];
                }
            }
            if (is_string($model)) {
                $processor = Mage::getModel($model);
            }
        }
        return $processor;
    }
}
