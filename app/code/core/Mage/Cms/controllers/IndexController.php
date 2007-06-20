<?php

class Mage_Cms_IndexController extends Mage_Core_Controller_Front_Action
{
    public function CmsNoRouteAction($coreRoute = null)
    {
        $page = Mage::getSingleton('cms/page')->load();
        if( !$page->getData() || !is_null($coreRoute) ) {
            $this->_forward('noroute');
        } else {
            $this->loadLayout();
            $contentBlock = $this->getLayout()->createBlock('core/template', 'cms.block');
            $contentBlock->setTemplate('cms/content.phtml');
            $contentBlock->assign('pageData', $page);

            $metaBlock = $this->getLayout()->createBlock('core/template', 'cms.meta.block');
            $metaBlock->setTemplate('cms/meta.phtml');
            $metaBlock->assign('pageData', $page);

            $this->getLayout()->getBlock('head.title')->setContents($page->getPageTitle());

            $this->getLayout()->getBlock('content')->append($contentBlock);
            $this->getLayout()->getBlock('head')->append($metaBlock);
            $this->renderLayout();
        }
    }
}