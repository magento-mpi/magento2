<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Controller\Onepage;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface as CustomerAccountService;
use Magento\Customer\Service\V1\CustomerMetadataServiceInterface as CustomerMetadataService;

class SaveShippingMethod extends \Magento\Checkout\Controller\Onepage
{
    /**
     * Sales Quote repository
     *
     * @var \Magento\Sales\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerAccountService $customerAccountService
     * @param CustomerMetadataService $customerMetadataService
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Sales\Model\QuoteRepository $quoteRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerAccountService $customerAccountService,
        CustomerMetadataService $customerMetadataService,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Sales\Model\QuoteRepository $quoteRepository
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $customerAccountService,
            $customerMetadataService,
            $coreRegistry,
            $translateInline,
            $formKeyValidator,
            $scopeConfig,
            $layoutFactory
        );
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Shipping method save action
     *
     * @return void
     */
    public function execute()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);
            // $result will contain error data if shipping method is empty
            if (!$result) {
                $this->_eventManager->dispatch(
                    'checkout_controller_onepage_save_shipping_method',
                    ['request' => $this->getRequest(), 'quote' => $this->getOnepage()->getQuote()]
                );
                $this->getOnepage()->getQuote()->collectTotals();
                $this->getResponse()->representJson(
                    $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result)
                );

                $result['goto_section'] = 'payment';
                $result['update_section'] = [
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                ];
                $result['update_progress'] = ['html' => $this->getProgressHtml($result['goto_section'])];
            }
            $this->quoteRepository->save($this->getOnepage()->getQuote()->collectTotals());
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result)
            );
        }
    }
}
