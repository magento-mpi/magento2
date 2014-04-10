<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order\Transaction\Grid;

class Collection extends \Magento\Sales\Model\Resource\Order\Payment\Transaction\Collection
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
     * Apply sorting and filtering to collection
     *
     * @return \Magento\Sales\Model\Resource\Order\Transaction\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $order = $this->registryManager->registry('current_order');
        if ($order) {
            $this->addOrderIdFilter($order->getId());
        }
        $this->addOrderInformation(array('increment_id'));
        $this->addPaymentInformation(array('method'));
    }
}
