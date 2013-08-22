<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Sales_Model_Order_Creditmemo_Total_Cost extends Magento_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * Collect total cost of refunded items
     *
     * @param Magento_Sales_Model_Order_Creditmemo $creditmemo
     * @return Magento_Sales_Model_Order_Creditmemo_Total_Cost
     */
    public function collect(Magento_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $baseRefundTotalCost = 0;
        foreach ($creditmemo->getAllItems() as $item) {
            if (!$item->getHasChildren()){
                $baseRefundTotalCost += $item->getBaseCost()*$item->getQty();
            }
        }
        $creditmemo->setBaseCost($baseRefundTotalCost);
        return $this;
    }
}
