<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order;

/**
 * Flat sales order payment resource
 */
class Payment extends AbstractOrder
{
    /**
     * Serializeable field: additional_information
     *
     * @var array
     */
    protected $_serializableFields = array('additional_information' => array(null, array()));

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_order_payment_resource';

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Entity\TypeFactory $eavEntityTypeFactory
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Entity\TypeFactory $eavEntityTypeFactory
    ) {
        parent::__construct($resource, $dateTime, $eventManager, $eavEntityTypeFactory);
    }

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
