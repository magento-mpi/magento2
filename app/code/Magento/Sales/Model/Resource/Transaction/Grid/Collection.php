<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Resource\Transaction\Grid;


class Collection extends \Magento\Sales\Model\Resource\Order\Payment\Transaction\Collection
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $registryManager = null;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Registry $registryManager
     * @param null $connection
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Registry $registryManager,
        $connection = null,
        \Magento\Framework\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->registryManager = $registryManager;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Resource initialization
     *
     * @return $this
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
        return $this;
    }
}
