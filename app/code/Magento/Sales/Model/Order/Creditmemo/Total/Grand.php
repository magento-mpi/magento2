<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Sales_Model_Order_Creditmemo_Total_Grand extends Magento_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Magento_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $grandTotal     = $creditmemo->getGrandTotal();
        $baseGrandTotal = $creditmemo->getBaseGrandTotal();

        $grandTotal+= $creditmemo->getAdjustmentPositive();
        $baseGrandTotal+= $creditmemo->getBaseAdjustmentPositive();

        $grandTotal-= $creditmemo->getAdjustmentNegative();
        $baseGrandTotal-= $creditmemo->getBaseAdjustmentNegative();

        $creditmemo->setGrandTotal($grandTotal);
        $creditmemo->setBaseGrandTotal($baseGrandTotal);

        $creditmemo->setAdjustment($creditmemo->getAdjustmentPositive()-$creditmemo->getAdjustmentNegative());
        $creditmemo->setBaseAdjustment($creditmemo->getBaseAdjustmentPositive()-$creditmemo->getBaseAdjustmentNegative());

        return $this;
    }
}
