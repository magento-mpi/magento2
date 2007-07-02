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
            ->addLink(__('new page'), __('new page title'));

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('adminhtml/cms_page')
        );

        $this->getLayout()->getBlock('left')
            ->append($this->getLayout()->createBlock('adminhtml/store_switcher'))
            ->append($this->getLayout()->createBlock('adminhtml/cms_page_tabs'));

        /*
        $toolbar = $this->getLayout()->createBlock('adminhtml/cms_toolbar_pageadd', 'cms.page_add_toolbar');
        $form = $this->getLayout()->createBlock('adminhtml/cms_page_form', 'cms.page_form');

        $this->getLayout()->getBlock('content')
            ->append($toolbar)
            ->append($form);
        */
        $this->renderLayout();
    }
}