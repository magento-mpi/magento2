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

/**
 * Session id in url container
 */
class Enterprise_PageCache_Model_Container_Urlsid extends Enterprise_PageCache_Model_Container_Abstract
{
    /*
     * Placeholders for URLs
     */
    const PH_URL_SID  = '{PH_URL_SID}';
    const PH_URL_PATH = '{PH_URL_PATH}';

    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        return 'CONTAINER_URLSID_' . md5($this->_placeholder->getAttribute('cache_id'));
    }

    /**
     * Set placeholders in some url segments of html content
     *
     * @param string $html
     * @return string
     */
    protected function _replaceUrls($html)
    {
        $urlsPh = array();
        preg_match_all('/href="(.+?)"/', $html, $urls);
        if (!empty($urls[1])) {
            $urls = $urls[1];
            foreach ($urls as $key => $url) {
                $url = parse_url($url);
                $url['path'] = self::PH_URL_PATH;
                $url['query'] = htmlspecialchars_decode($url['query']);
                parse_str($url['query'], $query);
                if (isset($query[Mage_Core_Model_Session_Abstract::SESSION_ID_QUERY_PARAM])) {
                    $query[Mage_Core_Model_Session_Abstract::SESSION_ID_QUERY_PARAM] = self::PH_URL_SID;
                    $url['query'] = urldecode(htmlspecialchars_decode(http_build_query($query)));
                }
                $urlsPh[] = $this->_build_url($url);
            }
            $html = str_replace($urls, $urlsPh, $html);
        }

        return $html;
    }

    /**
     * Fill placeholders by appropriate content
     *
     * @param string $html
     * @return string
     */
    protected function _restoreUrls($html)
    {
        $sid = '';
        $sidName  = $this->_placeholder->getAttribute('sid_name');
        $urlPath  = $this->_placeholder->getAttribute('url_path');
        if (isset($_COOKIE[$sidName])) {
            $sid = $_COOKIE[$sidName];
        }

        $search  = array(self::PH_URL_SID, self::PH_URL_PATH);
        $replace = array($sid, $urlPath);
        $html = str_replace($search, $replace, $html);

        return $html;
    }

    /**
     * Set placeholders in some URL segments before save to cache
     *
     * @param  string $blockContent
     * @return Enterprise_PageCache_Model_Container_Abstract
     */
    public function saveCache($blockContent)
    {
        $blockContent = $this->_replaceUrls($blockContent);
        return parent::saveCache($blockContent);
    }

    /**
     * Restore urls with placeholders after loaded cache
     *
     * @param  $id
     * @return bool|string
     */
    protected function _loadCache($id)
    {
        $block = parent::_loadCache($id);
        if ($block !== false) {
            $block = $this->_restoreUrls($block);
        }
        return $block;
    }  

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $block      = $this->_placeholder->getAttribute('block');
        $template   = $this->_placeholder->getAttribute('template');

        $block = new $block;
        $block->setTemplate($template);
        return $block->toHtml();
    }

    /**
     * Generate placeholder content before application was initialized and apply to page content if possible
     *
     * @param string $content
     * @return bool
     */
    public function applyWithoutApp(&$content)
    {
        if (!isset($_COOKIE[$this->_placeholder->getAttribute('sid_name')])) {
            return false;
        }
        return parent::applyWithoutApp($content);
    }

    /**
     * Build an URL from of array like parse_url() returns
     *
     * @param  array $url
     * @return string
     */
    private function _build_url(array $url)
    {
        return  ((isset($url['scheme'])) ? $url['scheme'] . '://' : '')
            . ((isset($url['user'])) ? $url['user'] . ((isset($url['pass'])) ? ':' . $url['pass'] : '') . '@' : '')
            . ((isset($url['host'])) ? $url['host'] : '')
            . ((isset($url['port'])) ? ':' . $url['port'] : '')
            . ((isset($url['path'])) ? $url['path'] : '')
            . ((isset($url['query'])) ? '?' . $url['query'] : '')
            . ((isset($url['fragment'])) ? '#' . $url['fragment'] : '');
    }
}
