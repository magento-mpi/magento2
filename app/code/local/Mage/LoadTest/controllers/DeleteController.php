<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  {copyright}
 * @license    {license_link}
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
        $this->_session = Mage::getSingleton('Mage_LoadTest_Model_Session');
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