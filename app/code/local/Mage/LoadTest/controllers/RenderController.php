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
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * LoadTest render front controller
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author     Victor Tihonchuk <victor@varien.com>
 */

class Mage_LoadTest_RenderController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->_redirect('*/index/');
    }

    public function categoriesAction()
    {
        $page = Mage::getModel('loadtest/page');
        /* @var $page Mage_LoadTest_Model_Page */
        $model = Mage::getModel('loadtest/renderer_catalog');
        /* @var $model Mage_LoadTest_Model_Renderer_Catalog */
        $model->setType('CATEGORY')
            ->setSroreIds(null)
            ->setNesting(2)
            ->setMinCount(2)
            ->setMaxCount(5)
            ->render();
        $page->pageStat($model, 'render');
    }

    public function productsAction()
    {
        $page = Mage::getModel('loadtest/page');
        /* @var $page Mage_LoadTest_Model_Page */
        $model = Mage::getModel('loadtest/renderer_catalog');
        /* @var $model Mage_LoadTest_Model_Renderer_Catalog */
        try {
            $model->setType('PRODUCT')
                ->setCountProducts(100)
                ->setMinCount(5) //min assign categories
                ->setMaxCount(10) //max assign categories
                ->setMinPrice(10)
                ->setMaxPrice(300)
                ->setMinWeight(10)
                ->setMaxWeight(999)
                ->setVisibility(4)
                ->setQty(5)
                ->setStartProductName(0) //append to number
                ->render();
        }
        catch (Exception $e) {
            $page->exception($e->getMessage());
        }
        $page->pageStat($model, 'render');
    }

    public function customersAction()
    {
        $page = Mage::getModel('loadtest/page');
        /* @var $page Mage_LoadTest_Model_Page */
        $model = Mage::getModel('loadtest/renderer_customer');
        /* @var $model Mage_LoadTest_Model_Renderer_Customer */
        $model
            ->setMinCount(100)
            ->setMaxCount(100)
            ->setGroupId(1)
            ->setEmailMask('qa__%s@varien.com')
            ->setPassword('123123')
            ->render();
        $page->pageStat($model, 'render');
    }

    public function reviewsAction()
    {
        $page = Mage::getModel('loadtest/page');
        /* @var $page Mage_LoadTest_Model_Page */
        $model = Mage::getModel('loadtest/renderer_review');
        /* @var $model Mage_LoadTest_Model_Renderer_Review */
        try {
            $model
                ->setMinCount(50)
                ->setMaxCount(250)
                ->render();
        }
        catch (Exception $e) {
            $page->exception($e->getMessage());
        }
        $page->pageStat($model, 'render');
    }

    public function tagsAction()
    {
        $page = Mage::getModel('loadtest/page');
        /* @var $page Mage_LoadTest_Model_Page */
        $model = Mage::getModel('loadtest/renderer_tag');
        /* @var $model Mage_LoadTest_Model_Renderer_Tag */
        try {
            $model
                ->setMinTags(10)
                ->setMaxTags(20)
                ->setMinCount(1) //min assign tag to products
                ->setMaxCount(250) //max assign tag to products
                ->render();
        }
        catch (Exception $e) {
            $page->exception($e->getMessage());
        }
        $page->pageStat($model, 'render');
    }

    public function quotesAction()
    {
        $page = Mage::getModel('loadtest/page');
        /* @var $page Mage_LoadTest_Model_Page */
        $model = Mage::getModel('loadtest/renderer_sales');
        /* @var $model Mage_LoadTest_Model_Renderer_Sales */
        try {
            $model->setType('QUOTE')
                ->setPaymentMethod('checkmo')
                ->setShippingMethod('freeshipping_freeshipping')
                ->setMinProducts(1)
                ->setMaxProducts(5)
                ->setCountQuotes(100)
                ->render();
        }
        catch (Exception $e) {
            $page->exception($e->getMessage());
        }
        $page->pageStat($model, 'render');
    }

    public function ordersAction()
    {
        $page = Mage::getModel('loadtest/page');
        /* @var $page Mage_LoadTest_Model_Page */
        $model = Mage::getModel('loadtest/renderer_sales');
        /* @var $model Mage_LoadTest_Model_Renderer_Sales */
        try {
            $model->setType('ORDER')
                ->setPaymentMethod('checkmo')
                ->setShippingMethod('freeshipping_freeshipping')
                ->setMinProducts(1)
                ->setMaxProducts(5)
                ->setCountOrders(100)
                ->render();
        }
        catch (Exception $e) {
            $page->exception($e->getMessage());
        }
        $page->pageStat($model, 'render');
    }
}