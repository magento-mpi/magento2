<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order;

use Magento\Framework\App\Resource;
use Magento\Framework\Stdlib\DateTime;
use Magento\Sales\Model\Resource\Attribute;
use Magento\Sales\Model\Increment;
use Magento\Sales\Model\Resource\Entity as SalesResource;

/**
 * Flat sales order payment resource
 */
class Payment extends SalesResource
{
    /**
     * Serializeable field: additional_information
     *
     * @var array
     */
    protected $_serializableFields = ['additional_information' => [null, []]];

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_order_payment_resource';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_flat_order_payment', 'entity_id');
    }
}
