<?php
/**
 * Adminhtml customer tax class controller
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
        $this->_initAction()
            ->_addBreadcrumb(__('Customer Tax Classes'), __('Customer Tax Classes'))
            ->_addContent(
        		$this->getLayout()->createBlock('adminhtml/tax_class_toolbar_add')
        		->assign('createUrl', Mage::getUrl('*/tax_class_customer/add'))
        		->assign('header', __('Customer Tax Classes'))
        	)
            ->_addContent($this->getLayout()->createBlock('adminhtml/tax_class_grid_default'))
            ->renderLayout();
    }

    public function addAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(__('Customer Tax Classes'), __('Customer Tax Classes'), Mage::getUrl('*/tax_class_customer'))
            ->_addBreadcrumb(__('New Customer Tax Class'), __('New Customer Tax Class'))
            ->_addContent(
                $this->getLayout()->createBlock('adminhtml/tax_class_toolbar_save')
                    ->assign('header', __('New Customer Tax Class'))
                    ->assign('form', $this->getLayout()->createBlock('adminhtml/tax_class_customer_form_add'))
            )
            ->renderLayout();
    }

    /**
     * Initialize action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_setActiveMenu('sales/tax/tax_classes_customer')
            ->_addBreadcrumb(__('Sales'), __('Sales'))
            ->_addBreadcrumb(__('Tax'), __('Tax'))
//            ->_addLeft($this->getLayout()->createBlock('adminhtml/tax_tabs', 'tax_tabs')->setActiveTab('tax_class_customer'))
        ;
        return $this;
    }

}