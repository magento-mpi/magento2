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

class Enterprise_PageCache_Model_Crawler extends Mage_Core_Model_Abstract
{
    const USER_AGENT = 'MagentoCrawler';
    
    protected function _construct()
    {
        $this->_init('enterprise_pagecache/crawler');
    }
    /**
     * 1. Get Url Content
     * 2. Get links from page
     * 3. depth
     * 4.
     */

    /**
     * Get internal links from page content
     *
     * @param string $pageContent
     * @return array
     */
    public function getUrls($pageContent)
    {
        $urls = array();
        preg_match_all(
            "/\s+href\s*=\s*[\"\']?([^\s\"\']+)[\"\'\s]+/ims",
            $pageContent,
            $urls
        );
        $urls = $urls[1];
        return $urls;
    }

    /**
     * Fetch Page content by url
     * 
     * @param string $url
     * @return string
     */
    public function fetchUrl($url)
    {
        $buffer = file_get_contents($url);
        return $buffer;
    }

    public function crawl()
    {
        $stmt = $this->_getResource()->getUrlStmt();
        $urls = array();
        $urlsCount = 0;
        $totalCount = 0;
        $adapter = new Varien_Http_Adapter_Curl();
        $options = array(
            CURLOPT_USERAGENT => self::USER_AGENT,
        );

        $start = microtime(true);
        while ($row = $stmt->fetch()) {
            $urls[] = Mage::app()->getStore($row['store_id'])->getBaseUrl().$row['request_path'];
            $options[CURLOPT_COOKIE] = 'store='.Mage::app()->getStore($row['store_id'])->getCode();
            $urlsCount++;
            $totalCount++;
            if ($urlsCount==5) {
                $batchStart = microtime(true);
                $adapter->multiRequest($urls, $options);
                $batchEnd = microtime(true);
                Mage::log('Batch Run Time:'.($batchEnd-$batchStart));
                $urlsCount = 0;
                $urls = array();
            }
            if ($totalCount>1000) {
                Mage::log('TOTAL Time:'.(microtime(true) -  $start));
                die();
            }
        }
    }
}
