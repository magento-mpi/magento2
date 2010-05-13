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

class Enterprise_PageCache_Model_Processor_Default
{
    /**
     * Get request uri based on HTTP request uri and visitor session state
     *
     * @param Enterprise_PageCache_Model_Processor $processor
     * @param Zend_Controller_Request_Http $request
     * @return string
     */
    public function getRequestUri(Enterprise_PageCache_Model_Processor $processor, Zend_Controller_Request_Http $request)
    {
        return $processor->getRequestId();
    }

    /**
     * Check if request can be cached
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function allowCache(Zend_Controller_Request_Http $request)
    {
        if (Mage::getSingleton('core/session')->getNoCacheFlag()) {
            return false;
        }
        return true;
    }

    /**
     * Prepare response body before caching
     *
     * @param Zend_Controller_Response_Http $response
     * @return string
     */
    public function prepareContent(Zend_Controller_Response_Http $response)
    {
        $start = microtime(true);
        $content = $response->getBody();
        $containers = array();
        preg_match_all(
            Enterprise_PageCache_Model_Container_Placeholder::HTML_NAME_PATTERN,
            $content,
            $containers,
            PREG_PATTERN_ORDER
        );
        $containers = array_unique($containers[1]);
        foreach ($containers as $container) {
            $placeholder= Mage::getModel('enterprise_pagecache/container_placeholder', $container);
            $pattern    = $placeholder->getPattern();
            $replacer   = $placeholder->getReplacer();
            $content = preg_replace($pattern, $replacer, $content);
        }
        return $content;
    }
}
