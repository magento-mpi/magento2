<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order\Status;

/**
 * Flat sales order status history resource
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class History extends \Magento\Sales\Model\Resource\Order\AbstractOrder
{
    /**
     * @var History\Validator
     */
    protected $validator;

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
        \Magento\Eav\Model\Entity\TypeFactory $eavEntityTypeFactory,
        History\Validator $validator
    ) {
        $this->validator = $validator;
        parent::__construct($resource, $dateTime, $eventManager, $eavEntityTypeFactory);
    }

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_order_status_history_resource';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_flat_order_status_history', 'entity_id');
    }

    /**
     * Perform actions before object save
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\Object $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_beforeSave($object);
        $warnings = $this->validator->validate($object);
        if (!empty($warnings)) {
            throw new \Magento\Framework\Model\Exception(
                __("Cannot save comment") . ":\n" . implode("\n", $warnings)
            );
        }
        return $this;
    }

}
