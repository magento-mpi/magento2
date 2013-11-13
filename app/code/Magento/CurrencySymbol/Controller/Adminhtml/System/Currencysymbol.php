<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CurrencySymbol
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Currency Symbols Controller
 *
 * @category    Magento
 * @package     currencysymbol
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CurrencySymbol\Controller\Adminhtml\System;

class Currencysymbol extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\App\Action\Title
     */
    protected $_title;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\App\Action\Title $title
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\App\Action\Title $title
    )
    {
        parent::__construct($context);
        $this->_title = $title;
    }

    /**
     * Show Currency Symbols Management dialog
     */
    public function indexAction()
    {
        // set active menu and breadcrumbs
        $this->_layoutServices->loadLayout();
        $this->_setActiveMenu('Magento_CurrencySymbol::system_currency_symbols')
            ->_addBreadcrumb(
                __('System'),
                __('System')
            )
            ->_addBreadcrumb(
                __('Manage Currency Rates'),
                __('Manage Currency Rates')
            );

        $this->_title->add(__('Currency Symbols'));
        $this->_layoutServices->renderLayout();
    }

    /**
     * Save custom Currency symbol
     */
    public function saveAction()
    {
        $symbolsDataArray = $this->getRequest()->getParam('custom_currency_symbol', null);
        if (is_array($symbolsDataArray)) {
            foreach ($symbolsDataArray as &$symbolsData) {
                /** @var $filterManager \Magento\Filter\FilterManager */
                $filterManager = $this->_objectManager->get('Magento\Filter\FilterManager');
                $symbolsData = $filterManager->stripTags($symbolsData);
            }
        }

        /** @var \Magento\Backend\Model\Session $backendSession */
        $backendSession = $this->_objectManager->get('Magento\Backend\Model\Session');
        try {
            $this->_objectManager->create('Magento\CurrencySymbol\Model\System\Currencysymbol')
                ->setCurrencySymbolsData($symbolsDataArray);
            $backendSession->addSuccess(__('The custom currency symbols were applied.'));
        } catch (\Exception $e) {
            $backendSession->addError($e->getMessage());
        }

        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
    }

    /**
     * Resets custom Currency symbol for all store views, websites and default value
     */
    public function resetAction()
    {
        $this->_objectManager->create('Magento\CurrencySymbol\Model\System\Currencysymbol')->resetValues();
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_CurrencySymbol::symbols');
    }
}
