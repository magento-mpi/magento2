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
    /**
     * Get viewed product ids from cookie
     */
    protected function _getProductIds()
    {
        if (isset($_COOKIE['VIEWED_PRODUCT_IDS'])) {
            return explode(',', $_COOKIE['VIEWED_PRODUCT_IDS']);
        }
        return array();
    }

    public function applyWithoutApp(&$content)
    {
        if ($this->_getProductIds()) {
//            $this->_applyToContent($content, $_COOKIE['VIEWED_PRODUCT_IDS']);
            return false;
        }
        return true;
    }

    public function applyInApp(&$content)
    {
        $block = $this->_placeholder->getAttribute('block');
        $template = $this->_placeholder->getAttribute('template');
        $block = new $block;
        $block->setTemplate($template);
        $block->setProductIds($this->_getProductIds());
        $blockContent = $block->toHtml();
        $this->_applyToContent($content, $blockContent);
        return true;
    }
}