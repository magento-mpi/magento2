<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class OrderStatus
 *
 */
class OrderStatus extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Sales\Test\Repository\OrderStatus';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Sales\Test\Handler\OrderStatus\OrderStatusInterface';

    protected $defaultDataSet = [
        'status' => 'order_status%isolation%',
        'label' => 'orderLabel%isolation%'
    ];

    protected $status = [
        'attribute_code' => 'status',
        'backend_type' => 'varchar',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $label = [
        'attribute_code' => 'label',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    public function getStatus()
    {
        return $this->getData('status');
    }

    public function getLabel()
    {
        return $this->getData('label');
    }
}
