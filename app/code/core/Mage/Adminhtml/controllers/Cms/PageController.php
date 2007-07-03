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
class Mage_Adminhtml_Cms_PageController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('cms');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('CMS'), __('cms title'));


        $block = $this->getLayout()->createBlock('adminhtml/cms', 'cms');
        $this->getLayout()->getBlock('content')->append($block);

        $this->renderLayout();
    }

    public function newpageAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('cms');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('CMS'), __('cms title'), Mage::getUrl('adminhtml/cms_page'))
            ->addLink(__(( $this->getRequest()->getParam('breadcrumb') ) ? $this->getRequest()->getParam('breadcrumb') : 'new page'),
                      __(( $this->getRequest()->getParam('breadcrumb_title') ) ? $this->getRequest()->getParam('breadcrumb_title') : 'new page title'));

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('adminhtml/cms_page')
        );

        $this->getLayout()->getBlock('left')
            ->append($this->getLayout()->createBlock('adminhtml/store_switcher'))
            ->append($this->getLayout()->createBlock('adminhtml/cms_page_tabs'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_forward('newpage', null, null, array('breadcrumb' => 'edit page', 'breadcrumb_title' => 'edit page title'));
    }

    public function deleteAction()
    {
        $pageId = intval( $this->getRequest()->getParam('page') );
        Mage::getModel('cms/page')->delete($pageId);
        $this->getResponse()->setRedirect( Mage::getUrl('adminhtml/cms') );
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

        $pageObject = new Varien_Object();
        $pageObject->setData($pageData);
        Mage::getModel('cms/page')->save($pageObject);

        $this->getResponse()->setRedirect( Mage::getUrl('adminhtml/cms') );
    }
}