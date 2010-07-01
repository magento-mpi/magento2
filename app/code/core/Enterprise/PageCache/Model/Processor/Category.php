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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_PageCache_Model_Processor_Category extends Enterprise_PageCache_Model_Processor_Default
{
    protected $_paramsMap = array(
        'display_mode'  => 'mode',
        'limit_page'    => 'limit',
        'sort_order'    => 'order',
        'sort_direction'=> 'dir',
    );
    /**
     * Get request uri based on HTTP request uri and visitor session state
     *
     * @param Enterprise_PageCache_Model_Processor $processor
     * @param Zend_Controller_Request_Http $request
     * @return string
     */
    public function getRequestUri(Enterprise_PageCache_Model_Processor $processor, Zend_Controller_Request_Http $request)
    {
        $requestId = $processor->getRequestId();
        $params = $this->_getSessionParams();
        $queryParams = $request->getQuery();
        $queryParams = array_merge($params, $queryParams);
        ksort($queryParams);

        $origQuery= http_build_query($request->getQuery());
        $newQuery = http_build_query($queryParams);
        if ($origQuery) {
            $requestId = str_replace($origQuery, $newQuery, $requestId);
        } else {
            if ($newQuery) {
                $requestId = $requestId . '?' . $newQuery;
            }
        }
        return $requestId;
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
        $data   = Mage::getSingleton('catalog/session')->getData();
        foreach ($this->_paramsMap as $sessionParam => $queryParam) {
            if (isset($data[$sessionParam])) {
                $params[$queryParam] = $data[$sessionParam];
            }
        }
        return $params;
    }
}
