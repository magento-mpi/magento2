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

/**
 * Abstract placeholder container
 */
abstract class Enterprise_PageCache_Model_Container_Abstract
{
    /**
     * Placeholder instance
     * @var Enterprise_PageCache_Model_Container_Placeholder
     */
    protected $_placeholder;

    /**
     * Class constructor
     *
     * @param Enterprise_PageCache_Model_Container_Placeholder $placeholder
     */
    public function __construct($placeholder)
    {
        $this->_placeholder = $placeholder;
    }

    /**
     * Get container individual cache id
     * @return string | false
     */
    protected function _getCacheId()
    {
        return false;
    }

    /**
     * Generate placeholder content before application was initialized and apply to page content if possible
     *
     * @param string $content
     * @return bool
     */
    public function applyWithoutApp(&$content)
    {
        $cacheId = $this->_getCacheId();
        if ($cacheId !== false) {
            $block = $this->_loadCache($cacheId);
            if ($block) {
                $this->_applyToContent($content, $block);
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * Generate and apply container content in controller after application is initialized
     *
     * @param string $content
     * @return bool
     */
    public function applyInApp(&$content)
    {
        return false;
    }

    /**
     * Relace conainer placeholder in content on container content
     *
     * @param string $content
     * @param string $containerContent
     */
    protected function _applyToContent(&$content, $containerContent)
    {
        $containerContent = $this->_placeholder->getStartTag() . $containerContent . $this->_placeholder->getEndTag();
        $content = str_replace($this->_placeholder->getReplacer(), $containerContent, $content);
    }

    /**
     * Load cached data by cache id
     * @param string $id
     * @return string | false
     */
    protected function _loadCache($id)
    {
        return Mage::app()->getCache()->load($id);
    }

    /**
     * Save data to cache storage
     * @param string $data
     * @param string $id
     * @param array $tags
     */
    protected function _saveCache($data, $id, $tags = array())
    {
        $tags[] = Enterprise_PageCache_Model_Processor::CACHE_TAG;
        $lifetime = $this->_placeholder->getAttribute('cache_lifetime');
        if (!$lifetime) {
            $lifetime = false;
            //$lifetime = 30 * 24 * 60 * 60;
        }
        Mage::app()->getCache()->save($data, $id, $tags, $lifetime);
        return $this;
    }
}
