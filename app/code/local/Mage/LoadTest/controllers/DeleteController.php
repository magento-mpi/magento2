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
 * LoadTest delete front controller
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author     Victor Tihonchuk <victor@varien.com>
 */

class Mage_LoadTest_DeleteController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->_redirect('*/index/');
    }

    public function categoriesAction()
    {
        $session = Mage::getModel('loadtest/session');
        /* @var $session Mage_LoadTest_Model_Session */
        $model = Mage::getModel('loadtest/renderer_catalog');
        /* @var $model Mage_LoadTest_Model_Renderer_Catalog */
        $model->setType('CATEGORY')
            ->delete();
        $session->prepareXmlResponse($model->getResult());
    }

    public function productsAction()
    {
        $session = Mage::getModel('loadtest/session');
        /* @var $session Mage_LoadTest_Model_Session */
        $model = Mage::getModel('loadtest/renderer_catalog');
        /* @var $model Mage_LoadTest_Model_Renderer_Catalog */
        $model->setType('PRODUCT')
            ->delete();
        $session->prepareXmlResponse($model->getResult());
    }

    public function customersAction()
    {
        $session = Mage::getModel('loadtest/session');
        /* @var $session Mage_LoadTest_Model_Session */
        $model = Mage::getModel('loadtest/renderer_customer');
        /* @var $model Mage_LoadTest_Model_Renderer_Customer */
        $model->delete();
        $session->prepareXmlResponse($model->getResult());
    }

    public function reviewsAction()
    {
        $session = Mage::getModel('loadtest/session');
        /* @var $session Mage_LoadTest_Model_Session */
        $model = Mage::getModel('loadtest/renderer_review');
        /* @var $model Mage_LoadTest_Model_Renderer_Review */
        $model->delete();
        $session->prepareXmlResponse($model->getResult());
    }

    public function tagsAction()
    {
        $session = Mage::getModel('loadtest/session');
        /* @var $session Mage_LoadTest_Model_Session */
        $model = Mage::getModel('loadtest/renderer_tag');
        /* @var $model Mage_LoadTest_Model_Renderer_Tag */
        $model->delete();
        $session->prepareXmlResponse($model->getResult());
    }

    public function quotesAction()
    {
        $session = Mage::getModel('loadtest/session');
        /* @var $session Mage_LoadTest_Model_Session */
        $model = Mage::getModel('loadtest/renderer_sales');
        /* @var $model Mage_LoadTest_Model_Renderer_Sales */
        $model->setType('QUOTE')
            ->delete();
        $session->prepareXmlResponse($model->getResult());
    }

    public function ordersAction()
    {
        $session = Mage::getModel('loadtest/session');
        /* @var $session Mage_LoadTest_Model_Session */
        $model = Mage::getModel('loadtest/renderer_sales');
        /* @var $model Mage_LoadTest_Model_Renderer_Sales */
        $model->setType('ORDER')
            ->delete();
        $session->prepareXmlResponse($model->getResult());
    }
}