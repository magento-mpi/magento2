<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Model\Observer;

class UploadSkuCsv
{
    /**
     * Checkout data
     *
     * @var \Magento\AdvancedCheckout\Helper\Data
     */
    protected $_checkoutData;

    /**
     * @var CartProvider
     */
    protected $cartProvider;

    /**
     * @param \Magento\AdvancedCheckout\Helper\Data $checkoutHelper
     * @param CartProvider $backendCartProvider
     */
    public function __construct(
        \Magento\AdvancedCheckout\Helper\Data $checkoutHelper,
        CartProvider $backendCartProvider
    ) {
        $this->_checkoutData = $checkoutHelper;
        $this->cartProvider = $backendCartProvider;
    }

    /**
     * Upload and parse CSV file with SKUs
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $helper \Magento\AdvancedCheckout\Helper\Data */
        $helper = $this->_checkoutData;
        $rows = $helper->isSkuFileUploaded(
            $observer->getRequestModel()
        ) ? $helper->processSkuFileUploading() : array();
        if (empty($rows)) {
            return;
        }

        /* @var $orderCreateModel \Magento\Sales\Model\AdminOrder\Create */
        $orderCreateModel = $observer->getOrderCreateModel();
        $cart = $this->cartProvider->get($observer);
        $cart->prepareAddProductsBySku($rows);
        $cart->saveAffectedProducts($orderCreateModel, false);
    }
}
