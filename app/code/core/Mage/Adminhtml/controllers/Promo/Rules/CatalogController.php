<?php

class Mage_Adminhtml_Promo_Rules_CatalogController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->loadLayout('baseframe');

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('promo/catalog');
        
        $this->getLayout()->getBlock('root')->setCanLoadRulesJs(true);

        /**
         * Append customers block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/rule_test', 'rule_test')
        );
        
        $this->renderLayout();
    }
}