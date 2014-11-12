<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order;

use Magento\Framework\Stdlib\DateTime;
use Magento\Sales\Model\Resource\Attribute;
use Magento\Framework\App\Resource as AppResource;
use Magento\Sales\Model\Increment as SalesIncrement;
use Magento\Sales\Model\Resource\Entity as SalesResource;
use Magento\Sales\Model\Resource\Order\Creditmemo\Grid as CreditmemoGrid;

/**
 * Flat sales order creditmemo resource
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Creditmemo extends SalesResource
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_order_creditmemo_resource';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_creditmemo', 'entity_id');
    }

    /**
     * Constructor
     *
     * @param AppResource $resource
     * @param DateTime $dateTime
     * @param Attribute $attribute
     * @param SalesIncrement $salesIncrement
     * @param CreditmemoGrid $gridAggregator
     */
    public function __construct(
        AppResource $resource,
        DateTime $dateTime,
        Attribute $attribute,
        SalesIncrement $salesIncrement,
        CreditmemoGrid $gridAggregator
    ) {
        parent::__construct($resource, $dateTime, $attribute, $salesIncrement, $gridAggregator);
    }
}
