<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Helper;

use Magento\Core\Model\Store;

/**
 * Sales module base helper
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Data extends \Magento\Core\Helper\Data
{
    /**
     * Maximum available number
     */
    const MAXIMUM_AVAILABLE_NUMBER = 99999999;

    /**
     * Check quote amount
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @param float $amount
     * @return $this
     */
    public function checkQuoteAmount(\Magento\Sales\Model\Quote $quote, $amount)
    {
        if (!$quote->getHasError() && ($amount>=self::MAXIMUM_AVAILABLE_NUMBER)) {
            $quote->setHasError(true);
            $quote->addMessage(
                __('This item price or quantity is not valid for checkout.')
            );
        }
        return $this;
    }

    /**
     * Check allow to send new order confirmation email
     *
     * @param null|string|bool|int|Store $store
     * @return bool
     */
    public function canSendNewOrderConfirmationEmail($store = null)
    {
        return $this->_coreStoreConfig->getConfigFlag(\Magento\Sales\Model\Order::XML_PATH_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send new order email
     *
     * @param null|string|bool|int|Store $store
     * @return bool
     */
    public function canSendNewOrderEmail($store = null)
    {
        return $this->canSendNewOrderConfirmationEmail($store);
    }

    /**
     * Check allow to send order comment email
     *
     * @param null|string|bool|int|Store $store
     * @return bool
     */
    public function canSendOrderCommentEmail($store = null)
    {
        return $this->_coreStoreConfig->getConfigFlag(\Magento\Sales\Model\Order::XML_PATH_UPDATE_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send new shipment email
     *
     * @param null|string|bool|int|Store $store
     * @return bool
     */
    public function canSendNewShipmentEmail($store = null)
    {
        return $this->_coreStoreConfig
            ->getConfigFlag(\Magento\Sales\Model\Order\Shipment::XML_PATH_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send shipment comment email
     *
     * @param null|string|bool|int|Store $store
     * @return bool
     */
    public function canSendShipmentCommentEmail($store = null)
    {
        return $this->_coreStoreConfig
            ->getConfigFlag(\Magento\Sales\Model\Order\Shipment::XML_PATH_UPDATE_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send new invoice email
     *
     * @param null|string|bool|int|Store $store
     * @return bool
     */
    public function canSendNewInvoiceEmail($store = null)
    {
        return $this->_coreStoreConfig
            ->getConfigFlag(\Magento\Sales\Model\Order\Invoice::XML_PATH_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send invoice comment email
     *
     * @param null|string|bool|int|Store $store
     * @return bool
     */
    public function canSendInvoiceCommentEmail($store = null)
    {
        return $this->_coreStoreConfig
            ->getConfigFlag(\Magento\Sales\Model\Order\Invoice::XML_PATH_UPDATE_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send new creditmemo email
     *
     * @param null|string|bool|int|Store $store
     * @return bool
     */
    public function canSendNewCreditmemoEmail($store = null)
    {
        return $this->_coreStoreConfig
            ->getConfigFlag(\Magento\Sales\Model\Order\Creditmemo::XML_PATH_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send creditmemo comment email
     *
     * @param null|string|bool|int|Store $store
     * @return bool
     */
    public function canSendCreditmemoCommentEmail($store = null)
    {
        return $this->_coreStoreConfig
            ->getConfigFlag(\Magento\Sales\Model\Order\Creditmemo::XML_PATH_UPDATE_EMAIL_ENABLED, $store);
    }
}
