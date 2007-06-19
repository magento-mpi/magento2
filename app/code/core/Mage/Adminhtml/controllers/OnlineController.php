<?php

class Mage_Adminhtml_OnlineController extends Mage_Core_Controller_Front_Action
{

    public function IndexAction()
    {
        $this->loadLayout('baseframe');
        $block = $this->getLayout()->createBlock('adminhtml/customers', 'customers');
        $this->getLayout()->getBlock('content')->append($block);

        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('online customers title'));

        $this->renderLayout();
    }
}
