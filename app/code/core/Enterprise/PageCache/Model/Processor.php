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
    const NO_CACHE_COOKIE           = 'NO_CACHE';
    const XML_NODE_ALLOWED_CACHE    = 'frontend/cache/requests';
    const XML_PATH_ALLOWED_DEPTH    = 'system/page_cache/allowed_depth';
    const XML_PATH_LIFE_TIME        = 'system/page_cache/lifetime';
    const REQUEST_ID_PREFIX         = 'REQEST_';
    const CACHE_TAG                 = 'FPC';  // Full Page Cache, minimize

    protected $_requestId;
    protected $_requestTags;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $uri = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        if (isset($_COOKIE['store'])) {
            $uri = $uri.'_'.$_COOKIE['store'];
        }
        if (isset($_COOKIE['currency'])) {
            $uri = $uri.'_'.$_COOKIE['currency'];
        }
        $this->_requestId  = self::REQUEST_ID_PREFIX.md5($uri);
        $this->_requestTags= array(self::CACHE_TAG);
    }

    /**
     * Get HTTP request identifier
     */
    public function getRequestId()
    {
        return $this->_requestId;
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
     * @return attay();
     */
    public function getRequestTags()
    {
        return $this->_requestTags;
    }

    /**
     * Check if processor is allowed for current HTTP request
     *
     * @return bool
     */
    public function isAllowed()
    {
        if (isset($_COOKIE['NO_CACHE'])) {
            return false;
        }
        return true;
    }

    /**
     * Check if processor can process specific HTTP request
     *
     * @param $request
     * @return bool
     */
    public function canProcessRequest(Zend_Controller_Request_Http $request)
    {
        $res = $this->isAllowed();
        $res = $res && Mage::app()->useCache('full_page');

        if ($res) {
            $maxDepth = Mage::getStoreConfig(self::XML_PATH_ALLOWED_DEPTH);
            $queryParams = $request->getQuery();
            $res = count($queryParams)<=$maxDepth;
        }
        if ($res) {
            $configuration = Mage::getConfig()->getNode(self::XML_NODE_ALLOWED_CACHE);
            if ($configuration) {
                $configuration = $configuration->asArray();
            }
            $module = $request->getModuleName();
            if (isset($configuration[$module])) {
                if ($configuration[$module] === '*') {
                    $res = true;
                } else {
                    $controller = $request->getControllerName();
                    if (isset($configuration[$module][$controller])) {
                        if ($configuration[$module][$controller] === '*') {
                            $res = true;
                        } else {
                            $action = $request->getActionName();
                            if (isset($configuration[$module][$controller][$action])) {
                                $res = ($configuration[$module][$controller][$action] === '*');
                            }
                        }
                    }
                }
            } else {
                $res = false;
            }
        }
        return $res;
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
            $content = Mage::app()->loadCache($this->getRequestId());
        }
        return $content;
    }

    /**
     * Remove dynamyc component (random generated blocks) from content
     *
     * @param string $content
     * @return string
     */
    protected function _stripDynamic($content)
    {
        $tags = array();
        preg_match_all("/<!--\[(.*?)-->/i", $content, $tags, PREG_PATTERN_ORDER);
        $tags = array_unique($tags[1]);
        foreach ($tags as $tag) {
            $content = preg_replace("/<!--\[{$tag}-->(.*?)<!--{$tag}\]-->/ims", '', $content);
        }
        return $content;
    }

    /**
     * Process http response
     *
     * @param Zend_Controller_Response_Http $response
     * @return Enterprise_PageCache_Model_Processor
     */
    public function process(Zend_Controller_Response_Http  $response)
    {
        $content = $response->getBody();
        $content = $this->_stripDynamic($content);
        $lifetime = Mage::getStoreConfig(self::XML_PATH_LIFE_TIME)*60;
        $content = Mage::app()->saveCache($content, $this->getRequestId(), $this->getRequestTags(), $lifetime);
        return $this;
    }

}
