<?php
/**
 * Adminhtml tax class customer controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Tax_Class_CustomerController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog');
        $this->_addBreadcrumb(__('Tax rules'), __('Tax rules title'), Mage::getUrl('adminhtml/tax'));
        $this->_addBreadcrumb(__('Customer tax classes'), __('Customer tax classes title'));

        $this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('adminhtml/store_switcher'));
        $this->getLayout()->getBlock('content')->append(
        		$this->getLayout()->createBlock('adminhtml/tax_class_toolbar_add')
        		->assign('createUrl', Mage::getUrl('adminhtml/tax_class_customer/add'))
        		->assign('header', __('Customer tax classes'))
        	);
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('adminhtml/tax_class_grid_default'));

        $this->renderLayout();
    }

    public function addAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog');
        $this->_addBreadcrumb(__('Tax rules'), __('Tax rules title'), Mage::getUrl('adminhtml/tax'));
        $this->_addBreadcrumb(__('Customer tax classes'), __('Customer tax classes title'), Mage::getUrl('adminhtml/tax_class_customer'));
        $this->_addBreadcrumb(__('New customer tax class'), __('New customer tax class title'));

        $this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('adminhtml/store_switcher'));
        $content = $this->getLayout()->getBlock('content');
        $content->append($this->getLayout()->createBlock('adminhtml/tax_class_toolbar_save'));
        $content->append($this->getLayout()->createBlock('adminhtml/tax_class_customer_form_add'));

        $this->renderLayout();
    }
}