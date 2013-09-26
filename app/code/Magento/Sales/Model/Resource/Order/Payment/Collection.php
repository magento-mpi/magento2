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
 * Flat sales order payment collection
 */
namespace Magento\Sales\Model\Resource\Order\Payment;

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
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Sales\Model\Payment\Method\Converter $converter
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Sales\Model\Payment\Method\Converter $converter,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->_converter = $converter;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
    }

    /**
     * Model initialization
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Order\Payment', 'Magento\Sales\Model\Resource\Order\Payment');
    }

    /**
     * Unserialize additional_information in each item
     *
     * @return \Magento\Sales\Model\Resource\Order\Payment\Collection
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
