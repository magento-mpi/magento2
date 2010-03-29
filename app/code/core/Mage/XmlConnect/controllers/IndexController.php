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
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect index controller
 *
 * @file        IndexController.php
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_IndexController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
    }

    public function indexAction()
    {
        $categoryModel = Mage::getResourceModel('xmlconnect/category_collection');

        /* TODO: Hardcoded banner */
        $additionalAttributes['home_banner'] = 'http://kd.varien.com/dev/yuriy.sorokolat/current/media/catalog/category/banner_home.png';
        $this->getResponse()->setBody(
            $categoryModel->addNameToResult()
                           ->addImageToResult()
                           ->addIsActiveFilter()
                           ->addLevelExactFilter(2)
                           ->addLimit(0,6)
                           ->load()
                           ->toXml($additionalAttributes)
        );
    }

    public function categoryAction() {
        if ($categoryId = $this->getRequest()->getParam('category_id', null))
        {
            $categoryModel = Mage::getModel('xmlconnect/category')->load($categoryId);
            if (!$categoryModel->hasChildren())
            {
                $productCollection = Mage::getResourceModel('xmlconnect/product_collection');
                $categoryModel->setProductCollection($productCollection);
                $productCollection->setStoreId($categoryModel->getStoreId())
                                  ->addCategoryFilter($categoryModel)
                                  ->addFiltersFromRequest($this->getRequest(), $categoryModel)
                                  ->addOrdersFromRequest($this->getRequest())
                                  ->addLimit($this->getRequest()->getParam('offset', 0),
                                             $this->getRequest()->getParam('count', 0));
                $this->getResponse()->setBody($productCollection->toXml());
                return;
            }
        }

        $categoryCollection = Mage::getResourceModel('xmlconnect/category_collection');
        $this->getResponse()->setBody(
            $categoryCollection->addImageToResult()
                                ->setStoreId($categoryCollection->getDefaultStoreId())
                                ->addParentIdFilter($categoryId)
                                ->addLimit($this->getRequest()->getParam('offset', 0),
                                           $this->getRequest()->getParam('count', 0))
                                ->toXml()
        );
    }

    public function filtersAction() {
        $categoryId = $this->getRequest()->getParam('category_id', null);
        $categoryModel = Mage::getModel('catalog/category')->load($categoryId);

        $sortOptions = $categoryModel->getAvailableSortByOptions();
        /* TODO: Here logic for sort options limiting to 3 items should be realized */
        $sortOptions = array_slice($sortOptions, 0, 3);

        $this->getResponse()->setBody(
            Mage::getModel('xmlconnect/filter_collection')
                ->setCategoryId($categoryId)
                ->toXml(array('orders'=>$sortOptions), true)
        );
    }


    public function productAction() {
        $product = Mage::getModel('xmlconnect/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($this->getRequest()->getParam('id', 0));
        /*$collection = $product->getRelatedProductCollection();
        $layer = Mage::getSingleton('catalog/layer')->prepareProductCollection($collection);
        $productCollection = Mage::getResourceModel('xmlconnect/product_collection');
        $this->getResponse()->setBody(
            $productCollection->toXml(array(),'item', false, 'relatedProducts', $collection)
        );*/

        $this->getResponse()->setBody(
            $product->toXml(array(), 'item', false, true, true)
        );
    }
}