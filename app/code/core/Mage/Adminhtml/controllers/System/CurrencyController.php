<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Currency controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
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
        $currency = Mage::getModel('Mage_Directory_Model_Currency')
            ->load($code);

        Mage::register('currency', $currency);
        return $this;
    }

    /**
     * Currency management main page
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Manage Currency Rates'));

        $this->loadLayout();
        $this->_setActiveMenu('system/currency');
        $this->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_System_Currency'));
        $this->renderLayout();
    }

    public function fetchRatesAction()
    {
        try {
            $service = $this->getRequest()->getParam('rate_services');
            $this->_getSession()->setCurrencyRateService($service);
            if( !$service ) {
                throw new Exception(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Invalid Import Service Specified'));
            }
            try {
                $importModel = Mage::getModel(Mage::getConfig()->getNode('global/currency/import/services/' . $service . '/model')->asArray());
            } catch (Exception $e) {
                Mage::throwException(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Unable to initialize import model'));
            }
            $rates = $importModel->fetchRates();
            $errors = $importModel->getMessages();
            if( sizeof($errors) > 0 ) {
                foreach ($errors as $error) {
                    Mage::getSingleton('Mage_Adminhtml_Model_Session')->addWarning($error);
                }
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addWarning(Mage::helper('Mage_Adminhtml_Helper_Data')->__('All possible rates were fetched, please click on "Save" to apply'));
            } else {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(Mage::helper('Mage_Adminhtml_Helper_Data')->__('All rates were fetched, please click on "Save" to apply'));
            }

            Mage::getSingleton('Mage_Adminhtml_Model_Session')->setRates($rates);
        }
        catch (Exception $e){
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    public function saveRatesAction()
    {
        $data = $this->getRequest()->getParam('rate');
        if( is_array($data) ) {
            try {
                foreach ($data as $currencyCode => $rate) {
                    foreach( $rate as $currencyTo => $value ) {
                        $value = abs(Mage::getSingleton('Mage_Core_Model_Locale')->getNumber($value));
                        $data[$currencyCode][$currencyTo] = $value;
                        if( $value == 0 ) {
                            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addWarning(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Invalid input data for %s => %s rate', $currencyCode, $currencyTo));
                        }
                    }
                }

                Mage::getModel('Mage_Directory_Model_Currency')->saveRates($data);
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(Mage::helper('Mage_Adminhtml_Helper_Data')->__('All valid rates have been saved.'));
            } catch (Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('system/currency');
    }
}
