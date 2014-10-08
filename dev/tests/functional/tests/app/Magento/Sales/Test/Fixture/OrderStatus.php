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

    protected $state = [
        'attribute_code' => 'state',
        'backend_type' => 'varchar',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $is_default = [
        'attribute_code' => 'is_default',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $visible_on_front = [
        'attribute_code' => 'visible_on_front',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
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

    public function getState()
    {
        return $this->getData('state');
    }

    public function getIsDefault()
    {
        return $this->getData('is_default');
    }

    public function getVisibleOnFront()
    {
        return $this->getData('visible_on_front');
    }
}
