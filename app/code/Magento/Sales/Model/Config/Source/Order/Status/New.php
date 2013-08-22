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
 * Order Statuses source model
 */
class Magento_Sales_Model_Config_Source_Order_Status_New extends Magento_Sales_Model_Config_Source_Order_Status
{
    protected $_stateStatuses = Magento_Sales_Model_Order::STATE_NEW;
}
