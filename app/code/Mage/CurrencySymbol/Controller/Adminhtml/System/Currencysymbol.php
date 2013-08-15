<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CurrencySymbol
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Currency Symbols Controller
 *
 * @category    Mage
 * @package     currencysymbol
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CurrencySymbol_Controller_Adminhtml_System_Currencysymbol extends Mage_Adminhtml_Controller_Action
{
    /**
     * Show Currency Symbols Management dialog
     */
    public function indexAction()
    {
        // set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('Mage_CurrencySymbol::system_currency_symbols')
            ->_addBreadcrumb(
                __('System'),
                __('System')
            )
            ->_addBreadcrumb(
                __('Manage Currency Rates'),
                __('Manage Currency Rates')
            );

        $this->_title(__('Currency Symbols'));
        $this->renderLayout();
    }

    /**
     * Save custom Currency symbol
     */
    public function saveAction()
    {
        $symbolsDataArray = $this->getRequest()->getParam('custom_currency_symbol', null);
        if (is_array($symbolsDataArray)) {
            foreach ($symbolsDataArray as &$symbolsData) {
                $symbolsData = Mage::helper('Mage_Adminhtml_Helper_Data')->stripTags($symbolsData);
            }
        }

        try {
            Mage::getModel('Mage_CurrencySymbol_Model_System_Currencysymbol')->setCurrencySymbolsData($symbolsDataArray);
            Mage::getSingleton('Mage_Connect_Model_Session')->addSuccess(
                __('The custom currency symbols were applied.')
            );
        } catch (Exception $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
        }

        $this->_redirectReferer();
    }

    /**
     * Resets custom Currency symbol for all store views, websites and default value
     */
    public function resetAction()
    {
        Mage::getModel('Mage_CurrencySymbol_Model_System_Currencysymbol')->resetValues();
        $this->_redirectReferer();
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_CurrencySymbol::symbols');
    }
}
