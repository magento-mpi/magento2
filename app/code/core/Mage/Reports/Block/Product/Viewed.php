<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Reports Recently Viewed Products Block
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Reports_Block_Product_Viewed extends Mage_Catalog_Block_Product_Abstract
{
    protected function _hasViewedProductsBefore()
    {
        return Mage::getSingleton('reports/session')->getData('viewed_products');
    }

    protected function _toHtml()
    {
        if ($this->_hasViewedProductsBefore() === false) {
            return '';
        }

        // get products collection and apply status and visibility filter
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addUrlRewrite()
            ->setPageSize(5)
            ->setCurPage(1)
        ;
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        // apply events log to collection with required parameters
        $skip = array();
        if (($product = Mage::registry('product')) && $product->getId()) {
            $skip = (int)$product->getId();
        }
        $subtype = 0;
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $subjectId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        } else {
            $subjectId = Mage::getSingleton('log/visitor')->getId();
            $subtype = 1;
        }
        Mage::getResourceSingleton('reports/event')->applyLogToCollection($collection, Mage_Reports_Model_Event::EVENT_PRODUCT_VIEW, $subjectId, $subtype, $skip);

        // load products collection and set session flag if viewed
        $hasProducts = false;
        foreach ($collection as $product) {
            $hasProducts = true;
            $product->setDoNotUseCategoryId(true);
        }
        if (is_null($this->_hasViewedProductsBefore())) {
            Mage::getSingleton('reports/session')->setData('viewed_products', $hasProducts);
        }

        $this->setRecentlyViewedProducts($collection);

        return parent::_toHtml();
    }
}