<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\SampleData\Module\CustomerBalance;

/**
 * Class Observer
 */
class Observer
{
    /**
     * @param \Magento\Framework\Object $params
     * @return mixed
     */
    public function getCreditmemoData(\Magento\Framework\Object $params)
    {
        /** @var \Magento\Sales\Model\Order\Item $orderItem */
        $orderItem = $params->getOrderItem();
        $data = $params->getCreditMemo();
        if ($orderItem->getOrder()->getBaseGrandTotal()) {
            $data['refund_customerbalance_return_enable'] = '1';
            $data['refund_customerbalance_return'] = $orderItem->getOrder()->getBaseGrandTotal();
        }

        return $data;
    }
}
