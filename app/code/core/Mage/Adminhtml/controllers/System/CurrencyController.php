<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Currency controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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
        $this->loadLayout();
        $this->_setActiveMenu('system/currency');
        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_currency'));
        $this->renderLayout();
    }

    public function importAction()
    {
        $importModel = Mage::getModel('directory/currency_import_webservicex');
        try {
            $importModel->importRates();
            Mage::getSingleton('adminhtml/session')->addSuccess(__('All rates were imported'));
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
        $this->loadLayout();
        $this->_setActiveMenu('system/currency');
        $this->_addLeft($this->getLayout()->createBlock('adminhtml/system_currency_edit_tabs'));
        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_currency_edit'));
        $this->renderLayout();
    }

    public function saveAction()
    {

    }*/

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('system/currency');
    }
}
