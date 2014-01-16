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

class Currency extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
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
        $currency = $this->_objectManager->create('Magento\Directory\Model\Currency')->load($code);

        $this->_coreRegistry->register('currency', $currency);
        return $this;
    }

    /**
     * Currency management main page
     */
    public function indexAction()
    {
        $this->_title->add(__('Currency Rates'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_CurrencySymbol::system_currency_rates');
        $this->_addContent($this->_view->getLayout()->createBlock('Magento\CurrencySymbol\Block\Adminhtml\System\Currency'));
        $this->_view->renderLayout();
    }

    public function fetchRatesAction()
    {
        /** @var \Magento\Backend\Model\Session $backendSession */
        $backendSession = $this->_objectManager->get('Magento\Backend\Model\Session');
        try {
            $service = $this->getRequest()->getParam('rate_services');
            $this->_getSession()->setCurrencyRateService($service);
            if (!$service) {
                throw new \Exception(__('Please specify a correct Import Service.'));
            }
            try {
                /** @var \Magento\Directory\Model\Currency\Import\ImportInterface $importModel */
                $importModel = $this->_objectManager->get('Magento\Directory\Model\Currency\Import\Factory')
                    ->create($service);
            } catch (\Exception $e) {
                throw new \Magento\Core\Exception(__('We can\'t initialize the import model.'));
            }
            $rates = $importModel->fetchRates();
            $errors = $importModel->getMessages();
            if (sizeof($errors) > 0) {
                foreach ($errors as $error) {
                    $this->messageManager->addWarning($error);
                }
                $this->messageManager->addWarning(__('All possible rates were fetched, please click on "Save" to apply'));
            } else {
                $this->messageManager->addSuccess(__('All rates were fetched, please click on "Save" to apply'));
            }

            $backendSession->setRates($rates);
        }
        catch (\Exception $e){
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('adminhtml/*/');
    }

    public function saveRatesAction()
    {
        $data = $this->getRequest()->getParam('rate');
        if (is_array($data)) {
            try {
                foreach ($data as $currencyCode => $rate) {
                    foreach( $rate as $currencyTo => $value ) {
                        $value = abs($this->_objectManager
                                ->get('Magento\Core\Model\LocaleInterface')
                                ->getNumber($value)
                        );
                        $data[$currencyCode][$currencyTo] = $value;
                        if( $value == 0 ) {
                            $this->messageManager->addWarning(
                                __('Please correct the input data for %1 => %2 rate', $currencyCode, $currencyTo)
                            );
                        }
                    }
                }

                $this->_objectManager->create('Magento\Directory\Model\Currency')->saveRates($data);
                $this->messageManager->addSuccess(__('All valid rates have been saved.'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        $this->_redirect('adminhtml/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_CurrencySymbol::currency_rates');
    }
}
