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
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * LoadTest render front controller
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_LoadTest_RenderController extends Mage_Core_Controller_Front_Action
{
    /**
     * Session model
     *
     * @var Mage_LoadTest_Model_Session
     */
    protected $_session;

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_session = Mage::getSingleton('loadtest/session');
        if (!$this->_session->isEnabled() || !$this->_session->isLoggedIn()) {
            die();
        }
    }

    public function categoriesAction()
    {
        $model = Mage::getModel('Mage_LoadTest_Model_Renderer_Catalog');
        /* @var $model Mage_LoadTest_Model_Renderer_Catalog */
        $model->setType('CATEGORY')
            ->setSroreIds($this->getRequest()->getParam('store_ids', null))
            ->setNesting($this->getRequest()->getParam('nesting', 2))
            ->setMinCount($this->getRequest()->getParam('min_count', 5))
            ->setMaxCount($this->getRequest()->getParam('max_count', 5))
            ->setCurrentCount($this->getRequest()->getParam('current_count', 0))
            ->setParentId(rawurldecode($this->getRequest()->getParam('parent_id', 0)))
            ->setPrefix(trim(rawurldecode($this->getRequest()->getParam('prefix', null))))
            ->setIncrement($this->getRequest()->getParam('increment', 0))
            ->setDetailLog($this->getRequest()->getParam('detail_log', 0))
            ->render();

        $this->_session->prepareXmlResponse($model->getResult());
    }

    public function attributesAction()
    {
        $model = Mage::getModel('Mage_LoadTest_Model_Renderer_Catalog');
        /* @var $model Mage_LoadTest_Model_Renderer_Catalog */
        $model->setType('ATTRIBUTE_SET')
            ->setText($this->getRequest()->getParam('text', 0))
            ->setTextarea($this->getRequest()->getParam('textarea', 0))
            ->setDate($this->getRequest()->getParam('date', 0))
            ->setBoolean($this->getRequest()->getParam('boolean', 0))
            ->setMultiselect(rawurldecode($this->getRequest()->getParam('multiselect', '0,0,0')))
            ->setSelect(rawurldecode($this->getRequest()->getParam('select', '0,0,0')))
            ->setPrice($this->getRequest()->getParam('price', 0))
            ->setImage($this->getRequest()->getParam('image', 0))
            ->setDetailLog($this->getRequest()->getParam('detail_log', 0))
            ->render();

        $this->_session->prepareXmlResponse($model->getResult());
    }

    public function simpleProductsAction()
    {
        $model = Mage::getModel('Mage_LoadTest_Model_Renderer_Catalog');
        /* @var $model Mage_LoadTest_Model_Renderer_Catalog */
        try {
            $model->setType('SIMPLE_PRODUCT')
                ->setCountProducts($this->getRequest()->getParam('count_products', 1000))
                ->setMinCount($this->getRequest()->getParam('min_count', 2)) //min assign categories
                ->setMaxCount($this->getRequest()->getParam('max_count', 10)) //max assign categories
                ->setMinPrice($this->getRequest()->getParam('min_price', 1))
                ->setMaxPrice($this->getRequest()->getParam('max_price', 300))
                ->setMinWeight($this->getRequest()->getParam('min_weight', 1))
                ->setMaxWeight($this->getRequest()->getParam('max_weight', 3))
                ->setVisibility($this->getRequest()->getParam('visibility', 4))
                ->setQty($this->getRequest()->getParam('qty', 5))
                ->setStartProductName($this->getRequest()->getParam('start_product_name', 0)) //append to number
                ->setAttributeSetId($this->getRequest()->getParam('attribute_set_id', 5))
                ->setFillAttribute($this->getRequest()->getParam('fill_attribute', 0)) // 0 required only, 1 all
                ->setDetailLog($this->getRequest()->getParam('detail_log', 0))
                ->render();
        }
        catch (Exception $e) {
            $model->exception($e->getMessage());
        }
        $this->_session->prepareXmlResponse($model->getResult());
    }

    public function customersAction()
    {
        $model = Mage::getModel('Mage_LoadTest_Model_Renderer_Customer');
        /* @var $model Mage_LoadTest_Model_Renderer_Customer */
        try {
            $model
                ->setCount($this->getRequest()->getParam('count', 100))
                ->setGroupId($this->getRequest()->getParam('group_id', 1))
                ->setEmailMask(rawurldecode($this->getRequest()->getParam('email_mask', 'qa__%s@varien.com')))
                ->setPassword(rawurldecode($this->getRequest()->getParam('password', '123123')))
                ->setDetailLog($this->getRequest()->getParam('detail_log', 0))
                ->render();
        }
        catch (Exception $e) {
            $model->exception($e->getMessage());
        }
        $this->_session->prepareXmlResponse($model->getResult());
    }

    public function reviewsAction()
    {
        $model = Mage::getModel('Mage_LoadTest_Model_Renderer_Review');
        /* @var $model Mage_LoadTest_Model_Renderer_Review */
        try {
            $model
                ->setCount($this->getRequest()->getParam('count', 1000))
                ->setDetailLog($this->getRequest()->getParam('detail_log', 0))
                ->render();
        }
        catch (Exception $e) {
            $model->exception($e->getMessage());
        }
        $this->_session->prepareXmlResponse($model->getResult());
    }

    public function tagsAction()
    {
        $model = Mage::getModel('Mage_LoadTest_Model_Renderer_Tag');
        /* @var $model Mage_LoadTest_Model_Renderer_Tag */
        try {
            $model
                ->setCount($this->getRequest()->getParam('count', 100))
                ->setMinAssign($this->getRequest()->getParam('min_assign', 1)) //min assign tag to products
                ->setMaxAssign($this->getRequest()->getParam('max_assign', 1000)) //max assign tag to products
                ->setDetailLog($this->getRequest()->getParam('detail_log', 0))
                ->render();
        }
        catch (Exception $e) {
            $model->exception($e->getMessage());
        }
        $this->_session->prepareXmlResponse($model->getResult());
    }

    public function quotesAction()
    {
        $model = Mage::getModel('Mage_LoadTest_Model_Renderer_Sales');
        /* @var $model Mage_LoadTest_Model_Renderer_Sales */
        try {
            $model->setType('QUOTE')
                ->setPaymentMethod($this->getRequest()->getParam('payment_method', 'checkmo'))
                ->setShippingMethod($this->getRequest()->getParam('shipping_method', 'freeshipping_freeshipping'))
                ->setMinProducts($this->getRequest()->getParam('min_products', 1))
                ->setMaxProducts($this->getRequest()->getParam('max_products', 5))
                ->setCountQuotes($this->getRequest()->getParam('count_quotes', 250))
                ->setDetailLog($this->getRequest()->getParam('detail_log', 0))
                ->render();
        }
        catch (Exception $e) {
            $model->exception($e->getMessage());
        }
        $this->_session->prepareXmlResponse($model->getResult());
    }

    public function ordersAction()
    {
        $model = Mage::getModel('Mage_LoadTest_Model_Renderer_Sales');
        /* @var $model Mage_LoadTest_Model_Renderer_Sales */
        try {
            $model->setType('ORDER')
                ->setPaymentMethod($this->getRequest()->getParam('payment_method', 'checkmo'))
                ->setShippingMethod($this->getRequest()->getParam('shipping_method', 'freeshipping_freeshipping'))
                ->setMinProducts($this->getRequest()->getParam('min_products', 1))
                ->setMaxProducts($this->getRequest()->getParam('max_products', 3))
                ->setCountOrders($this->getRequest()->getParam('count_orders', 250))
                ->setYearAgo($this->getRequest()->getParam('year_ago', 2))
                ->setDetailLog($this->getRequest()->getParam('detail_log', 0))
                ->render();
        }
        catch (Exception $e) {
            $model->exception($e->getMessage());
        }
        $this->_session->prepareXmlResponse($model->getResult());
    }
}