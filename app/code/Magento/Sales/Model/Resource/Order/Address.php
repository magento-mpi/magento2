<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order;

/**
 * Flat sales order address resource
 */
class Address extends AbstractOrder
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_order_address_resource';

    /**
     * @var \Magento\Sales\Model\Resource\Factory
     */
    protected $_salesResourceFactory;

    /**
     * @var \Magento\Sales\Model\Order\Address\Validator
     */
    protected $_validator;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Entity\TypeFactory $eavEntityTypeFactory
     * @param \Magento\Sales\Model\Resource\Factory $salesResourceFactory
     * @param \Magento\Sales\Model\Order\Address\Validator $validator
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Entity\TypeFactory $eavEntityTypeFactory,
        \Magento\Sales\Model\Resource\Factory $salesResourceFactory,
        \Magento\Sales\Model\Order\Address\Validator $validator
    ) {
        $this->_validator = $validator;
        parent::__construct($resource, $dateTime, $eventManager, $eavEntityTypeFactory);
        $this->_salesResourceFactory = $salesResourceFactory;

    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_flat_order_address', 'entity_id');
    }

    /**
     * Return configuration for all attributes
     *
     * @return array
     */
    public function getAllAttributes()
    {
        $attributes = array(
            'city' => __('City'),
            'company' => __('Company'),
            'country_id' => __('Country'),
            'email' => __('Email'),
            'firstname' => __('First Name'),
            'lastname' => __('Last Name'),
            'region_id' => __('State/Province'),
            'street' => __('Street Address'),
            'telephone' => __('Telephone'),
            'postcode' => __('Zip/Postal Code')
        );
        asort($attributes);
        return $attributes;
    }

    /**
     * Performs validation before save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_beforeSave($object);
        $this->_validator->validate($object);
        return $this;

    }

    /**
     * Update related grid table after object save
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\Object $object
     * @return \Magento\Framework\Model\Resource\Db\AbstractDb
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $resource = parent::_afterSave($object);
        if ($object->hasDataChanges() && $object->getOrder()) {
            $gridList = array(
                'Magento\Sales\Model\Resource\Order' => 'entity_id',
                'Magento\Sales\Model\Resource\Order\Invoice' => 'order_id',
                'Magento\Sales\Model\Resource\Order\Shipment' => 'order_id',
                'Magento\Sales\Model\Resource\Order\Creditmemo' => 'order_id'
            );

            // update grid table after grid update
            foreach ($gridList as $gridResource => $field) {
                $this->_salesResourceFactory->create(
                    $gridResource
                )->updateOnRelatedRecordChanged(
                    $field,
                    $object->getParentId()
                );
            }
        }

        return $resource;
    }
}
