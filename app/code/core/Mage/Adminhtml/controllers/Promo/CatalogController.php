<?php

class Mage_Adminhtml_Promo_CatalogController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('promo/catalog');
        $this->_addBreadcrumb(__('Price Rules'), __('Price Rules'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/promo_catalog'));
        $this->renderLayout();
    }
}