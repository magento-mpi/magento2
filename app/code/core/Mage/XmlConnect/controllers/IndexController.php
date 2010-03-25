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

    public function indexAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
        $categoryModel = Mage::getResourceModel('xmlconnect/category_collection');

        /* TODO: Hardcoded banner */
        $additionalAttributes['home_banner'] = 'http://kd.varien.com/dev/yuriy.sorokolat/current/media/catalog/category/banner_home.png';

        echo $categoryModel->addNameToResult()
                           ->addImageToResult()
                           ->addIsActiveFilter()
                           ->addLevelExactFilter(2)
                           ->addLimit(0,6)
                           ->load()
                           ->toXml($additionalAttributes);
    }

    public function getAllCategoriesAction() {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
        $categoryModel = Mage::getResourceModel('xmlconnect/category_collection');
        echo $categoryModel->addNameToResult()
                           ->addImageToResult()
                           ->setStoreId($categoryModel->getDefaultStoreId())
                           ->addIsActiveFilter()
                           ->addParentIdFilter($this->getRequest()->getParam('parent_id', null))
                           ->addLimit($this->getRequest()->getParam('offset', 0),
                                      $this->getRequest()->getParam('count', 0))
                           ->toXml();
    }

    public function getCategoryContentAction() {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        if ($categoryId = $this->getRequest()->getParam('category_id', null))
        {
            $categoryModel = Mage::getModel('catalog/category')->load($categoryId);
            if (!$categoryModel->hasChildren())
            {
                $productCollection = Mage::getResourceModel('xmlconnect/product_collection')
                                            ->setStoreId($categoryModel->getStoreId())
                                            ->addCategoryFilter($categoryModel)
                                            ->addOrdersFromRequest($this->getRequest())
                                            //->addFiltersFromRequest($this->getRequest())
                                            ->addLimit($this->getRequest()->getParam('offset', 0),
                                                       $this->getRequest()->getParam('count', 0));
                echo $productCollection->toXml();
                return;
            }
        }

        $categoryCollection = Mage::getResourceModel('xmlconnect/category_collection');
        echo $categoryCollection->addImageToResult()
                                ->setStoreId($categoryCollection->getDefaultStoreId())
                                ->addParentIdFilter($categoryId)
                                ->addLimit($this->getRequest()->getParam('offset', 0),
                                           $this->getRequest()->getParam('count', 0))
                                ->toXml();
    }

    public function getFiltersAction() {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $categoryId = $this->getRequest()->getParam('category_id', null);
        $categoryModel = Mage::getModel('catalog/category')->load($categoryId);

        $sortOptions = $categoryModel->getAvailableSortByOptions();
        /* TODO: Here logic for sort options limiting to 3 items should be realized */
        $sortOptions = array_slice($sortOptions, 0, 3);

        echo Mage::getModel('xmlconnect/filter_collection')
                   ->setCategoryId($categoryId)
                   ->toXml(array('orders'=>$sortOptions), true);
    }

    public function testAction() {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $categoryModel = Mage::getModel('catalog/category')->load($this->getRequest()->getParam('category_id', null));
        echo Mage::getResourceModel('xmlconnect/product_collection')
                   ->addCategoryFilter($categoryModel)
                   ->addFiltersFromRequest($this->getRequest())
                   ->toXml();
    }
}