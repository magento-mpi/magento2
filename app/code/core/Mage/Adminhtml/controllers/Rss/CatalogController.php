<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Rss Controller
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Rss_CatalogController extends Mage_Adminhtml_Controller_Action
{
    public function preDispatch()
    {
        $path = '';
        if ($this->getRequest()->getActionName() == 'review') {
            $path = 'catalog/reviews_ratings';
        } elseif ($this->getRequest()->getActionName() == 'notifystock') {
            $path = 'catalog/products';
        }
        Mage::helper('Mage_Adminhtml_Helper_Rss')->authAdmin($path);
        parent::preDispatch();
        return $this;
    }

    public function notifystockAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function reviewAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
    }
}
