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
namespace Magento\Sales\Model\Config\Source\Order\Status;

class New extends \Magento\Sales\Model\Config\Source\Order\Status
{
    protected $_stateStatuses = \Magento\Sales\Model\Order::STATE_NEW;
}
