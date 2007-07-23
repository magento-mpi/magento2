<?php

class Mage_Adminhtml_Promo_Rules_CatalogController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->loadLayout('baseframe');

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('customer/manage');

        /**
         * Append customers block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/customers', 'customers')
        );

        /**
         * Add breadcrumb item
         */
        $this->_addBreadcrumb(__('Catalog price rules'));

        $this->renderLayout();
    }
}