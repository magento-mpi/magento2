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
 * Placeholder container for catalog navigation block
 */
class Enterprise_PageCache_Model_Container_Catalognavigation extends Enterprise_PageCache_Model_Container_Abstract
{
    /**
     * Get container individual cache id
     *
     * @return string | false
     */
    protected function _getCacheId()
    {
        if ($this->_placeholder->getAttribute('name') === 'catalog.topnav') {
            return false;
        }
        return $this->_placeholder->getAttribute('cache_id');
    }

    /**
     * @return string
     */
    protected function _getBlockCacheId()
    {
        return $this->_placeholder->getAttribute('short_cache_id');
    }

    /**
     * @return string
     */
    protected function _getCategoryCacheId()
    {
        $shortCacheId = $this->_placeholder->getAttribute('short_cache_id');
        $categoryPath = $this->_placeholder->getAttribute('category_path');
        if (!$shortCacheId || !$categoryPath) {
            return false;
        }
        return $shortCacheId . '_' . $categoryPath;
    }

    /**
     * Generate placeholder content before application was initialized and apply to page content if possible
     *
     * @param string $content
     * @return bool
     */
    public function applyWithoutApp(&$content)
    {
        if ($this->_getCacheId()) {
            return parent::applyWithoutApp($content);
        }
        $blockCacheId = $this->_getBlockCacheId();
        $categoryCacheId = $this->_getCategoryCacheId();
        if ($blockCacheId && $categoryCacheId) {
            $blockContent = $this->_loadCache($blockCacheId);
            $categoryUniqueClasses = $this->_loadCache($categoryCacheId);
            if ($blockContent && $categoryUniqueClasses !== false) {
                if ($categoryUniqueClasses != '') {
                    $regexp = '';
                    foreach (explode(' ', $categoryUniqueClasses) as $categoryUniqueClass) {
                        $regexp .= ($regexp ? '|' : '') . preg_quote($categoryUniqueClass);
                    }
                    $blockContent = preg_replace('/(?<=\s|")(' . $regexp . ')(?=\s|")/u', '$1 active', $blockContent);
                }
                $this->_applyToContent($content, $blockContent);
                return true;
            }
        }
        return false;
    }

    /**
     * Generate block content
     *
     * @param $content
     */
    public function applyInApp(&$content)
    {
        $block = $this->_placeholder->getAttribute('block');
        $template = $this->_placeholder->getAttribute('template');
        $categoryPath = $this->_placeholder->getAttribute('category_path');

        /** @var Mage_Catalog_Block_Product_Price $block */
        $block = new $block;
        $block->setTemplate($template);

        if ($categoryPath) {
            $categoryPath = explode('/', $categoryPath);
            $categoryId = end($categoryPath);
            if (!Mage::registry('current_category')) {
                $category = Mage::getModel('catalog/category')->load($categoryId);
                Mage::register('current_category', $category);
            }
        }

        $blockContent = $block->toHtml();
        $this->_applyToContent($content, $blockContent);

        if ($cacheId = $this->_getCacheId()) {
            $this->_saveCache($blockContent, $cacheId);
            return true;
        }

        $blockCacheId = $this->_getBlockCacheId();
        if ($blockCacheId) {
            $categoryCacheId = $this->_getCategoryCacheId();
            if ($categoryCacheId) {
                $categoryUniqueClasses = '';
                $classes = array();
                $classesCount = preg_match_all('/(?<=\s)class="(.*?active.*?)"/u', $blockContent, $classes);
                for ($i = 0; $i < $classesCount; $i++) {
                    $classAttribute = $classes[0][$i];
                    $classValue = $classes[1][$i];
                    $classInactive = preg_replace('/\s+active|active\s+|active/', '', $classAttribute);
                    $blockContent = str_replace($classAttribute, $classInactive, $blockContent);
                    $matches = array();
                    if (preg_match('/(?<=\s|^)nav-.+?(?=\s|$)/', $classValue, $matches)) {
                        $categoryUniqueClasses .= ($categoryUniqueClasses ? ' ' : '') . $matches[0];
                    }
                }
                $this->_saveCache($categoryUniqueClasses, $categoryCacheId);
            }
            if (!Mage::app()->getCache()->test($blockCacheId)) {
                $this->_saveCache($blockContent, $blockCacheId);
            }
        }

        return true;
    }
}
