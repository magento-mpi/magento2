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
 * Cart sidebar container
 */
class Enterprise_PageCache_Model_Container_CartSidebar extends Enterprise_PageCache_Model_Container_Abstract
{
    const CART_COOKIE = 'CART';
    /**
     * Get cart hash from cookies
     */
    protected function _getCartIdentificator()
    {
        return (isset($_COOKIE[self::CART_COOKIE])) ? $_COOKIE[self::CART_COOKIE] : '';
    }

    /**
     * Get cache identifier
     */
    protected function _getCacheId()
    {
        return 'CONTAINER_SIDEBAR_'.md5($this->_placeholder->getAttribute('cache_id') . $this->_getCartIdentificator());
    }
    /**
     * Generate block content
     * @param $content
     */
    public function applyInApp(&$content)
    {
        $template = $this->_placeholder->getAttribute('template');
        $block = Mage::app()->getLayout()->createBlock('checkout/cart_sidebar');
        $block->setTemplate($template);
        $blockContent = $block->toHtml();
        $cacheId = $this->_getCacheId();
        if ($cacheId) {
            $this->_saveCache($blockContent, $cacheId, array($this->_getCartIdentificator()));
        }
        $this->_applyToContent($content, $blockContent);
        return true;
    }
}
