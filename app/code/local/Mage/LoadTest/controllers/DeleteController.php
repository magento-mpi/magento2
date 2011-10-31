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
 * LoadTest delete front controller
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_LoadTest_DeleteController extends Mage_Core_Controller_Front_Action
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
            ->delete();
        $this->_session->prepareXmlResponse($model->getResult());
    }

    public function productsAction()
    {
        $model = Mage::getModel('Mage_LoadTest_Model_Renderer_Catalog');
        /* @var $model Mage_LoadTest_Model_Renderer_Catalog */
        $model->setType('PRODUCT')
            ->delete();
        $this->_session->prepareXmlResponse($model->getResult());
    }

    public function customersAction()
    {
        $model = Mage::getModel('Mage_LoadTest_Model_Renderer_Customer');
        /* @var $model Mage_LoadTest_Model_Renderer_Customer */
        $model->delete();
        $this->_session->prepareXmlResponse($model->getResult());
    }

    public function reviewsAction()
    {
        $model = Mage::getModel('Mage_LoadTest_Model_Renderer_Review');
        /* @var $model Mage_LoadTest_Model_Renderer_Review */
        $model->delete();
        $this->_session->prepareXmlResponse($model->getResult());
    }

    public function tagsAction()
    {
        $model = Mage::getModel('Mage_LoadTest_Model_Renderer_Tag');
        /* @var $model Mage_LoadTest_Model_Renderer_Tag */
        $model->delete();
        $this->_session->prepareXmlResponse($model->getResult());
    }

    public function quotesAction()
    {
        $model = Mage::getModel('Mage_LoadTest_Model_Renderer_Sales');
        /* @var $model Mage_LoadTest_Model_Renderer_Sales */
        $model->setType('QUOTE')
            ->delete();
        $this->_session->prepareXmlResponse($model->getResult());
    }

    public function ordersAction()
    {
        $model = Mage::getModel('Mage_LoadTest_Model_Renderer_Sales');
        /* @var $model Mage_LoadTest_Model_Renderer_Sales */
        $model->setType('ORDER')
            ->delete();
        $this->_session->prepareXmlResponse($model->getResult());
    }
}