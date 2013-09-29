<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Flat sales order payment resource
 */
namespace Magento\Sales\Model\Resource\Order;

class Payment extends \Magento\Sales\Model\Resource\Order\AbstractOrder
{
    /**
     * Serializeable field: additional_information
     *
     * @var array
     */
    protected $_serializableFields   = array(
        'additional_information' => array(null, array())
    );

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix          = 'sales_order_payment_resource';

    /**
     * @var \Magento\Sales\Model\Payment\Method\Converter
     */
    protected $_paymentConverter;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Eav\Model\Entity\TypeFactory $eavEntityTypeFactory
     * @param \Magento\Sales\Model\Payment\Method\Converter $paymentConverter
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Resource $resource,
        \Magento\Eav\Model\Entity\TypeFactory $eavEntityTypeFactory,
        \Magento\Sales\Model\Payment\Method\Converter $paymentConverter
    ) {
        $this->_paymentConverter = $paymentConverter;
        parent::__construct($eventManager, $resource, $eavEntityTypeFactory);
    }

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_converter = $this->_paymentConverter;
        $this->_init('sales_flat_order_payment', 'entity_id');
    }
}
