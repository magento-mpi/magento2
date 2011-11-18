<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Sales_Model_Order_Creditmemo_Total_Cost extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * Collect total cost of refunded items
     *
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return Mage_Sales_Model_Order_Creditmemo_Total_Cost
     */
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
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
