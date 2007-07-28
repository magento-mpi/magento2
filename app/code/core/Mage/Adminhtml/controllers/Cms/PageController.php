<?php
/**
 * sales admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Cms_PageController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('cms/control');
        $this->_addBreadcrumb(__('CMS'), __('CMS Title'));

        $block = $this->getLayout()->createBlock('adminhtml/cms', 'cms');
        $this->_addContent($block);

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/cms_grid')->toHtml());
    }

    public function newpageAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('cms/control');
        $this->_addBreadcrumb(__('CMS'), __('CMS Title'));
        $this->_addBreadcrumb(__(( $this->getRequest()->getParam('breadcrumb') ) ? $this->getRequest()->getParam('breadcrumb') : 'New Page'),
                      __(( $this->getRequest()->getParam('breadcrumb_title') ) ? $this->getRequest()->getParam('breadcrumb_title') : 'New Page Title'));

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/cms_page')
        );

        $this->getLayout()->getBlock('left')
            //->append($this->getLayout()->createBlock('adminhtml/store_switcher'))
            ->append($this->getLayout()->createBlock('adminhtml/cms_page_tabs'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_forward('newpage', null, null, array('breadcrumb' => __('Edit Page'), 'breadcrumb_title' => __('Edit Page Title')));
    }

    public function deleteAction()
    {
        $pageId = intval( $this->getRequest()->getParam('page') );
        Mage::getModel('cms/page')->delete($pageId);
        $this->_redirect('adminhtml/cms');
    }

    public function enableAction()
    {
        $pageId = intval( $this->getRequest()->getParam('page') );
        Mage::getModel('cms/page')->enablePage($pageId);
        $this->_redirect('adminhtml/cms');
    }

    public function disableAction()
    {
        $pageId = intval( $this->getRequest()->getParam('page') );
        try {
            Mage::getModel('cms/page')->disablePage($pageId);
            $this->_redirect('adminhtml/cms');
        } catch (Exception $e) {
            /* FIXME!!! */
            $this->_redirect('adminhtml/cms');
        }
    }

    public function saveAction()
    {
        $pageData = array(
                'page_id' => $this->getRequest()->getParam('page_id', null),
                'page_title' => $this->getRequest()->getParam('page_title'),
                'page_identifier' => $this->getRequest()->getParam('page_identifier'),
                'page_active' => intval( $this->getRequest()->getParam('page_active') ),
                'page_content' => $this->getRequest()->getParam('page_content'),
                'page_meta_keywords' => $this->getRequest()->getParam('page_meta_keywords'),
                'page_meta_description' => $this->getRequest()->getParam('page_meta_description'),
                'page_store_id' => $this->getRequest()->getParam('page_store_id', 0) /* FIXME!!! */
            );

        $model = Mage::getModel('cms/page')->setData($pageData);
        if( $model->itemExists() === false ) {
            $model->save();
            $this->_redirect('adminhtml/cms');
        } else {
            Mage::getSingleton('adminhtml/session')->addError('Error while saving this page. Page with the same identifier already exists.');
            $this->_returnLocation();
        }
    }

    protected function _returnLocation()
    {
        if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
            $this->getResponse()->setRedirect($referer);
        }
    }
}
