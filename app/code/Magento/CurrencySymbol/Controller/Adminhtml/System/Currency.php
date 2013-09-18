<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Currency controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CurrencySymbol\Controller\Adminhtml\System;

class Currency extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init currency by currency code from request
     *
     * @return \Magento\CurrencySymbol\Controller\Adminhtml\System\Currency
     */
    protected function _initCurrency()
    {
        $code = $this->getRequest()->getParam('currency');
        $currency = \Mage::getModel('Magento\Directory\Model\Currency')
            ->load($code);

        $this->_coreRegistry->register('currency', $currency);
        return $this;
    }

    /**
     * Currency management main page
     */
    public function indexAction()
    {
        $this->_title(__('Currency Rates'));

        $this->loadLayout();
        $this->_setActiveMenu('Magento_CurrencySymbol::system_currency_rates');
        $this->_addContent($this->getLayout()->createBlock('Magento\CurrencySymbol\Block\Adminhtml\System\Currency'));
        $this->renderLayout();
    }

    public function fetchRatesAction()
    {
        try {
            $service = $this->getRequest()->getParam('rate_services');
            $this->_getSession()->setCurrencyRateService($service);
            if (!$service) {
                throw new \Exception(__('Please specify a correct Import Service.'));
            }
            try {
                $importModel = \Mage::getModel(
                    \Mage::getConfig()->getNode('global/currency/import/services/' . $service . '/model')->asArray()
                );
            } catch (\Exception $e) {
                \Mage::throwException(__('We can\'t initialize the import model.'));
            }
            $rates = $importModel->fetchRates();
            $errors = $importModel->getMessages();
            if (sizeof($errors) > 0) {
                foreach ($errors as $error) {
                    \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addWarning($error);
                }
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addWarning(__('All possible rates were fetched, please click on "Save" to apply'));
            } else {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('All rates were fetched, please click on "Save" to apply'));
            }

            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setRates($rates);
        }
        catch (\Exception $e){
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    public function saveRatesAction()
    {
        $data = $this->getRequest()->getParam('rate');
        if (is_array($data)) {
            try {
                foreach ($data as $currencyCode => $rate) {
                    foreach( $rate as $currencyTo => $value ) {
                        $value = abs(\Mage::getSingleton('Magento\Core\Model\LocaleInterface')->getNumber($value));
                        $data[$currencyCode][$currencyTo] = $value;
                        if( $value == 0 ) {
                            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addWarning(__('Please correct the input data for %1 => %2 rate', $currencyCode, $currencyTo));
                        }
                    }
                }

                \Mage::getModel('Magento\Directory\Model\Currency')->saveRates($data);
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('All valid rates have been saved.'));
            } catch (\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_CurrencySymbol::currency_rates');
    }
}
