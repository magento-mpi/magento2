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
 * Default placeholder container
 */
class Enterprise_PageCache_Model_Container_Viewedproducts extends Enterprise_PageCache_Model_Container_Abstract
{
    const COOKIE_NAME = 'VIEWED_PRODUCT_IDS';
    /**
     * Get viewed product ids from cookie
     */
    protected function _getProductIds()
    {
        if (isset($_COOKIE[self::COOKIE_NAME])) {
            return explode(',', $_COOKIE[self::COOKIE_NAME]);
        }
        return array();
    }

    /**
     * Get cache identifier
     */
    protected function _getCacheId()
    {
        $id = $this->_placeholder->getAttribute('cache_id');
        if ($id && $this->_getProductIds()) {
            $id = 'CONTAINER_'.md5($id . implode('_', $this->_getProductIds()));
            return $id;
        }
        return false;
    }

    /**
     * Generate block content
     * @param $content
     */
    public function applyInApp(&$content)
    {
        $block = $this->_placeholder->getAttribute('block');
        $template = $this->_placeholder->getAttribute('template');
        $productIds = $this->_getProductIds();
        $block = new $block;
        $block->setTemplate($template);
        $block->setProductIds($productIds);
        $blockContent = $block->toHtml();
        $this->_registerProductsView($productIds);
        $cacheId = $this->_getCacheId();
        if ($cacheId) {
            $this->_saveCache($blockContent, $cacheId);
        }
        $this->_applyToContent($content, $blockContent);
        return true;
    }

    /**
     * Save information about last viewed products
     * @param array $productIds
     */
    protected function _registerProductsView($productIds)
    {
        try {
            Mage::getModel('reports/product_index_viewed')->registerIds($productIds);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}