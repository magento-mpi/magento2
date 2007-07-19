<?php
/**
 * Adminhtml tax class product controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Tax_Class_ProductController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(__('Product Tax Classes'), __('Product Tax Classes Title'))
            ->_addContent(
        		$this->getLayout()->createBlock('adminhtml/tax_class_toolbar_add')
            		->assign('createUrl', Mage::getUrl('adminhtml/tax_class_product/add/class_type/PRODUCT'))
            		->assign('header', __('Product Tax Classes'))
        	)
        	->_addContent($this->getLayout()->createBlock('adminhtml/tax_class_grid_default')->setClassType('PRODUCT'))
            ->renderLayout();
    }

    public function addAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(__('Product Tax Classes'), __('Product Tax Classes Title'), Mage::getUrl('adminhtml/tax_class_product'))
            ->_addBreadcrumb(__('New Product Tax Class'), __('New Product Tax Class Title'))
            ->_addContent(
                $this->getLayout()->createBlock('adminhtml/tax_class_toolbar_save')
                    ->assign('header', __('New Product Tax Class'))
                    ->assign('form', $this->getLayout()->createBlock('adminhtml/tax_class_product_form_add'))
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
            ->_setActiveMenu('sales/tax/tax_rule')
            ->_addBreadcrumb(__('Sales'), __('Sales Title'))
            ->_addBreadcrumb(__('Tax'), __('Tax Title'))
            ->_addLeft($this->getLayout()->createBlock('adminhtml/tax_tabs', 'tax_tabs')->setActiveTab('tax_class_product'))
        ;
        return $this;
    }

}
