<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base class for credit memo total
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Sales_Model_Order_Creditmemo_Total_Abstract extends Magento_Sales_Model_Order_Total_Abstract
{
    /**
     * Collect credit memo subtotal
     *
     * @param Magento_Sales_Model_Order_Creditmemo $creditmemo
     * @return Magento_Sales_Model_Order_Creditmemo_Total_Abstract
     */
    public function collect(Magento_Sales_Model_Order_Creditmemo $creditmemo)
    {
        return $this;
    }
}
