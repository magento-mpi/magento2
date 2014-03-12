<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order\Payment;

/**
 * Flat sales order payment collection
 */
class Collection extends \Magento\Sales\Model\Resource\Order\Collection\AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_payment_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_payment_collection';

    /**
     * @var \Magento\Sales\Model\Payment\Method\Converter
     */
    protected $_converter;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Sales\Model\Payment\Method\Converter $converter
     * @param \Zend_Db_Adapter_Abstract $connection
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Sales\Model\Payment\Method\Converter $converter,
        $connection = null,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->_converter = $converter;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Model initialization\
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Order\Payment', 'Magento\Sales\Model\Resource\Order\Payment');
    }

    /**
     * Unserialize additional_information in each item
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $this->getResource()->unserializeFields($item);
        }

        /** @var \Magento\Sales\Model\Order\Payment $item */
        foreach ($this->_items as $item) {
            foreach ($item->getData() as $fieldName => $fieldValue) {
                $item->setData($fieldName,
                    $this->_converter->decode($item, $fieldName)
                );
            }
        }

        return parent::_afterLoad();
    }
}
