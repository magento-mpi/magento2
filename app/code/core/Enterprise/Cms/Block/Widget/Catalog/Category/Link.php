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
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Widget to display link to the category
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */

class Enterprise_Cms_Block_Widget_Catalog_Category_Link
    extends Mage_Core_Block_Template
    implements Mage_Cms_Block_Widget_Interface
{
    /**
     * Rendering category's link
     *
     * @return string
     */
    protected function _toHtml()
    {
        $urlRewriteResource = Mage::getResourceSingleton('enterprise_cms/url_rewrite');
        /* @var $urlRewriteResource Enterprise_Cms_Model_Mysql4_Url_Rewrite */

        $store = Mage::app()->getStore();
        $requestPath = null;

        $urlKey = $this->_getData('url_key');
        if ($urlKey) {
            $requestPath = $urlRewriteResource->retrieveRequestPathByUrlKey($urlKey, $store);
        }

        if ($requestPath) {
            $_attributes = array(
                'href' => $this->getBaseUrl(). $requestPath,
                'title' => is_null($this->_getData('title')) ? $this->_getData('anchor_text') : $this->_getData('title')
            );

            return Mage::helper('enterprise_cms')->prepareAnchorHtml(
                $this->_getData('anchor_text'), $_attributes);
        }

        return '';
    }
}
