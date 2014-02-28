<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Config\Source\Order\Status;

/**
 * Order Statuses source model
 */
class Processing extends \Magento\Sales\Model\Config\Source\Order\Status
{
    /**
     * @var string
     */
    protected $_stateStatuses = \Magento\Sales\Model\Order::STATE_PROCESSING;
}
