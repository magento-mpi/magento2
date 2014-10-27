<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Fixture rma entity.
 */
class Rma extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Rma\Test\Repository\Rma';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Rma\Test\Handler\Rma\RmaInterface';

    protected $defaultDataSet = [
        'is_active' => null,
        'date_requested' => null,
    ];

    protected $entity_id = [
        'attribute_code' => 'entity_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $status = [
        'attribute_code' => 'status',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => null
    ];

    protected $is_active = [
        'attribute_code' => 'is_active',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '1',
        'input' => '',
    ];

    protected $increment_id = [
        'attribute_code' => 'increment_id',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $date_requested = [
        'attribute_code' => 'date_requested',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => 'CURRENT_TIMESTAMP',
        'input' => '',
    ];

    protected $order_id = [
        'attribute_code' => 'order_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => null,
        'source' => 'Magento\Rma\Test\Fixture\Rma\OrderId',
    ];

    protected $order_increment_id = [
        'attribute_code' => 'order_increment_id',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $store_id = [
        'attribute_code' => 'store_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_id = [
        'attribute_code' => 'customer_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_custom_email = [
        'attribute_code' => 'customer_custom_email',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $protect_code = [
        'attribute_code' => 'protect_code',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $contact_email = [
        'attribute_code' => 'contact_email',
        'backend_type' => 'virtual',
        'group' => 'general',
    ];

    protected $comment = [
        'attribute_code' => 'comment',
        'backend_type' => 'virtual',
        'group' => 'general',
    ];

    protected $items = [
        'attribute_code' => 'items',
        'backend_type' => 'virtual',
        'group' => 'items',
        'source' => 'Magento\Rma\Test\Fixture\Rma\Items',
    ];

    public function getEntityId()
    {
        return $this->getData('entity_id');
    }

    public function getStatus()
    {
        return $this->getData('status');
    }

    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    public function getIncrementId()
    {
        return $this->getData('increment_id');
    }

    public function getDateRequested()
    {
        return $this->getData('date_requested');
    }

    public function getOrderId()
    {
        return $this->getData('order_id');
    }

    public function getOrderIncrementId()
    {
        return $this->getData('order_increment_id');
    }

    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    public function getCustomerId()
    {
        return $this->getData('customer_id');
    }

    public function getCustomerCustomEmail()
    {
        return $this->getData('customer_custom_email');
    }

    public function getProtectCode()
    {
        return $this->getData('protect_code');
    }

    public function getContactEmail()
    {
        return $this->getData('contact_email');
    }

    public function getComment()
    {
        return $this->getData('comment');
    }

    public function getItems()
    {
        return $this->getData('items');
    }
}
