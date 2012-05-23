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

class Mage_Rss_OrderController extends Mage_Rss_Controller_AdminhtmlAbstract
{
    /**
     * Returns map of action to acl paths, needed to check user's access to a specific action
     *
     * @return array
     */
    protected function _getAdminAclMap()
    {
        return array(
            'new' => 'sales/order'
        );
    }

    public function newAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function customerAction()
    {
        if (Mage::app()->getStore()->isCurrentlySecure()) {
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            Mage::helper('Mage_Rss_Helper_Data')->authFrontend();
        } else {
            $this->_redirect('rss/order/customer', array('_secure'=>true));
            return $this;
        }
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
