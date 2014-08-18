<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Service\V1\Action;

/**
 * Class OrderCreate
 */
class OrderCreate
{
    /**
     * @var \Magento\Sales\Model\OrderConverter
     */
    protected $orderConverter;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Sales\Model\OrderConverter $orderConverter
     * @param \Magento\Framework\Logger $logger
     */
    public function __construct(
        \Magento\Sales\Model\OrderConverter $orderConverter,
        \Magento\Framework\Logger $logger
    ) {
        $this->orderConverter = $orderConverter;
        $this->logger = $logger;
    }

    /**
     * Create order
     *
     * @param \Magento\Sales\Service\V1\Data\Order $invoiceDataObject
     * @return bool
     * @throws \Exception
     */
    public function invoke(\Magento\Sales\Service\V1\Data\Order $invoiceDataObject)
    {
        try {
            $order = $this->orderConverter->getModel($invoiceDataObject);
            return (bool)$order->save();
        } catch (\Exception $e) {
            $this->logger->logException($e);
            throw new \Exception(__('An error has occurred during order creation'));
        }
    }
}
