<?php
class Mage_Cms_IndexController extends Mage_Core_Controller_Front_Action
{
    public function CmsNoRouteAction($coreRoute = null)
    {
        $pageId = ( $this->getRequest()->getPathInfo() ) ? trim($this->getRequest()->getPathInfo(), '/') : null;
        $pageResource = Mage::getSingleton('cms/page');
        $page = $pageResource->load($pageId);

        if( !$page->getPageId() || !is_null($coreRoute) ) {
            $this->_forward('noroute');
            return;
        }
        $this->loadLayout();
        $contentBlock = $this->getLayout()->createBlock('core/template', 'cms.block');
        $contentBlock->setTemplate('cms/content.phtml');
        $contentBlock->assign('pageData', $page);

        $metaBlock = $this->getLayout()->getBlock('head');
        $metaBlock->setKeywords($page->getPageMetaKeywords());
        $metaBlock->setDescription($page->getPageMetaDescription());
        $metaBlock->setTitle($page->getPageTitle());

        $this->getLayout()->getBlock('content')->append($contentBlock);
        $this->renderLayout();
    }
}
