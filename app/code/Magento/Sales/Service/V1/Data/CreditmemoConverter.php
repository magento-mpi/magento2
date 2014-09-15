<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

/**
 * Class CreditmemoConverter
 *
 * @package Magento\Sales\Service\V1\Data
 */
class CreditmemoConverter
{
    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
     */
    public function __construct(\Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader)
    {
        $this->creditmemoLoader = $creditmemoLoader;
    }

    /**
     * @param Creditmemo $dataObject
     * @return bool|\Magento\Sales\Model\Order\Creditmemo
     */
    public function getModel(Creditmemo $dataObject)
    {
        $this->creditmemoLoader->setOrderId($dataObject->getOrderId());
        $this->creditmemoLoader->setCreditmemoId($dataObject->getEntityId());

        $items = [];
        foreach ($dataObject->getItems() as $item) {
            $items[$item->getOrderItemId()] = ['qty' => $item->getQty()];
        }
        $creditmemo = [
            'items' => $items,
            'shipping_amount' => $dataObject->getShippingAmount(),
            'adjustment_positive' => $dataObject->getAdjustmentPositive(),
            'adjustment_negative' => $dataObject->getAdjustmentNegative(),
        ];
        $this->creditmemoLoader->setCreditmemo($creditmemo);
        $this->creditmemoLoader->setInvoiceId($dataObject->getInvoiceId());
        return $this->creditmemoLoader->load();
    }
}
