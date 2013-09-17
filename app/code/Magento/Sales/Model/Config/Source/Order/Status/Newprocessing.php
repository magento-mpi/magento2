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
class Magento_Sales_Model_Config_Source_Order_Status_Newprocessing extends Magento_Sales_Model_Config_Source_Order_Status
{
    protected $_stateStatuses = array(
        Magento_Sales_Model_Order::STATE_NEW,
        Magento_Sales_Model_Order::STATE_PROCESSING
    );
}
