<?php
/**
 * Currency controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_System_CurrencyController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init currency by currency code from request
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initCurrency()
    {
        $code = $this->getRequest()->getParam('currency');
        $currency = Mage::getModel('directory/currency')
            ->load($code);
        
        Mage::register('currency', $currency);
        return $this;
    }
    
    /**
     * Currency management main page
     */
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('system/currency');
        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_currency'));
        $this->renderLayout();
    }
    
    public function importAction()
    {
        $importModel = Mage::getModel('directory/currency_import_webservicex');
        try {
            $importModel->importRates();
            Mage::getSingleton('adminhtml/session')->addSuccess('All rates were imported');
        }
        catch (Exception $e){
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
    
    /*public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function editAction()
    {
        $this->_initCurrency();
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('system/currency');
        $this->_addLeft($this->getLayout()->createBlock('adminhtml/system_currency_edit_tabs'));
        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_currency_edit'));
        $this->renderLayout();
    }
    
    public function saveAction()
    {
        
    }*/
}
