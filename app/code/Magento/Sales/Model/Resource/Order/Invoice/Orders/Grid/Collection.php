<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order\Invoice\Orders\Grid;

class Collection extends \Magento\Sales\Model\Resource\Order\Invoice\Grid\Collection
{
    /**
     * @var \Magento\Registry
     */
    protected $registryManager;

    /**
     * @param \Magento\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Registry $registryManager
     * @param null $connection
     * @param \Magento\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Data\Collection\EntityFactoryInterface $entityFactory,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Registry $registryManager,
        $connection = null,
        \Magento\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->registryManager = $registryManager;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->registryManager->registry('current_order');
    }


    /**
     * Apply sorting and filtering to collection
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToSelect(
            'entity_id'
        )->addFieldToSelect(
            'created_at'
        )->addFieldToSelect(
            'order_id'
        )->addFieldToSelect(
            'increment_id'
        )->addFieldToSelect(
            'state'
        )->addFieldToSelect(
            'grand_total'
        )->addFieldToSelect(
            'base_grand_total'
        )->addFieldToSelect(
            'store_currency_code'
        )->addFieldToSelect(
            'base_currency_code'
        )->addFieldToSelect(
            'order_currency_code'
        )->addFieldToSelect(
            'billing_name'
        )->setOrderFilter(
            $this->getOrder()
        );
        return $this;
    }
}
