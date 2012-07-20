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

class Mage_Rss_CatalogController extends Mage_Rss_Controller_AdminhtmlAbstract
{
    /**
     * Returns map of action to acl paths, needed to check user's access to a specific action
     *
     * @return array
     */
    protected function _getAdminAclMap()
    {
        return array(
            'notifystock' => 'catalog/products',
            'review' => 'catalog/reviews_ratings'
        );
    }

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
            $this->getResponse()->setHeader('BugsCoverage','404 File not found');
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
}
