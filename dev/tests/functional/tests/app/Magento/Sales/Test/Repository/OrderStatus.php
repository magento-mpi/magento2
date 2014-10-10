<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class OrderStatus
 * Repository for order status
 */
class OrderStatus extends AbstractRepository
{
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['orderStatus_assign_to_pending'] = [
            'status' => 'order_status%isolation%',
            'label' => 'orderLabel%isolation%',
            'state' => 'Pending',
            'is_default' => 'Yes',
            'visible_on_front' => 'Yes'
        ];
    }
}
