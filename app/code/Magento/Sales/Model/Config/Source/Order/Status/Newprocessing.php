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
class Newprocessing extends \Magento\Sales\Model\Config\Source\Order\Status
{
    /**
     * @var array
     */
    protected $_stateStatuses = array(
        \Magento\Sales\Model\Order::STATE_NEW,
        \Magento\Sales\Model\Order::STATE_PROCESSING
    );
}
