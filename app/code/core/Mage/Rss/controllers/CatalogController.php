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

class Mage_Rss_CatalogController extends Mage_Core_Controller_Front_Action
{
    protected function isFeedEnable($code)
    {
        return Mage::getStoreConfig('rss/catalog/'.$code);
    }

    protected function checkFeedEnable($code)
    {
        if ($this->isFeedEnable($code)) {
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            return true;
        } else {
            $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
            $this->getResponse()->setHeader('Status','404 File not found');
            $this->_forward('nofeed','index','rss');
            return false;
        }
    }

    public function newAction()
    {
        $this->checkFeedEnable('new');
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function specialAction()
    {
        $this->checkFeedEnable('special');
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function salesruleAction()
    {
        $this->checkFeedEnable('salesrule');
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function tagAction()
    {
        if ($this->checkFeedEnable('tag')) {
            $tagName = urldecode($this->getRequest()->getParam('tagName'));
            $tagModel = Mage::getModel('Mage_Tag_Model_Tag');
            $tagModel->loadByName($tagName);
            if ($tagModel->getId() && $tagModel->getStatus()==$tagModel->getApprovedStatus()) {
                Mage::register('tag_model', $tagModel);
                $this->loadLayout(false);
                $this->renderLayout();
                return;
            }
        }
        $this->_forward('nofeed', 'index', 'rss');
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

    public function categoryAction()
    {
        if ($this->checkFeedEnable('category')) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }

    /**
     * Controller predispatch method to change area for some specific action.
     *
     * @return Mage_Rss_CatalogController
     */
    public function preDispatch()
    {
        $path = null;
        switch ($this->getRequest()->getActionName()) {
            case 'notifystock':
                $path = 'catalog/products';
                break;
            case 'review':
                $path = 'catalog/reviews_ratings';
                break;
        }

        if ($path) {
            $user = Mage::helper('Mage_Rss_Helper_Data')->authAdmin($path);
            if (!is_object($user)) {
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                return $this;
            }
        }

        return parent::preDispatch();
    }
}
