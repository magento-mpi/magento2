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

        $user = Mage::helper('Mage_Adminhtml_Helper_Rss')->authAdmin($path);
        if (!is_object($user)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return $this;
        }

        parent::preDispatch();

        $this->getRequest()->setDispatched(true);
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
