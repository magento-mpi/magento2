<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order Statuses source model
 */
class Mage_Backend_Model_Config_Source_Order_Status_Processing extends Mage_Backend_Model_Config_Source_Order_Status
{
    protected $_stateStatuses = Mage_Sales_Model_Order::STATE_PROCESSING;
}
