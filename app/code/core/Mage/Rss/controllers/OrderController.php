<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer reviews controller
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Rss_OrderController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        if ('new' === $this->getRequest()->getActionName()) {
            $this->setCurrentArea('adminhtml');
            $session = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
            list($login, $password) = Mage::helper('Mage_Core_Helper_Http')->getHttpAuthCredentials($this->getRequest());
            if (!Mage::helper('Mage_Rss_Helper_Data')->isAdminAuthorized($session, $login, $password, 'sales/order')) {
                Mage::helper('Mage_Core_Helper_Http')->failHttpAuthentication($this->getResponse(), 'RSS Feeds');
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                return;
            }
        }
        parent::preDispatch();
    }

    public function newAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Order status action
     */
    public function statusAction()
    {
        $order = Mage::helper('Mage_Rss_Helper_Order')->getOrderByStatusUrlKey((string)$this->getRequest()->getParam('data'));
        if (!is_null($order)) {
            Mage::register('current_order', $order);
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            $this->loadLayout(false);
            $this->renderLayout();
            return;
        }
        $this->_forward('nofeed', 'index', 'rss');
    }
}
