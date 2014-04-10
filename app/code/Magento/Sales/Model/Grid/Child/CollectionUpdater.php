<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Grid\Child;


class CollectionUpdater implements \Magento\View\Layout\Argument\UpdaterInterface
{
    /**
     * @var \Magento\Registry
     */
    protected $registryManager;

    /**
     * @var \Magento\Sales\Model\Resource\Order\Payment\Transaction\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Registry $registryManager
     * @param \Magento\Sales\Model\Resource\Order\Payment\Transaction\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Registry $registryManager,
        \Magento\Sales\Model\Resource\Order\Payment\Transaction\CollectionFactory $collectionFactory
    ) {
        $this->registryManager = $registryManager;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param mixed $argument
     * @return mixed
     */
    public function update($argument)
    {
        $argument = $this->collectionFactory->create();
        $argument->addParentIdFilter($this->registryManager->registry('current_transaction')->getId());
        return $argument;
    }
}
