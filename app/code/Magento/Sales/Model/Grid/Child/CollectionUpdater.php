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
     * @param \Magento\Registry $registryManager
     */
    public function __construct(
        \Magento\Registry $registryManager
    ) {
        $this->registryManager = $registryManager;
    }

    /**
     * Update grid collection according to chosen transaction
     *
     * @param \Magento\Sales\Model\Resource\Transaction\Grid\Collection $argument
     * @return \Magento\Sales\Model\Resource\Transaction\Grid\Collection
     */
    public function update($argument)
    {
        $argument->addParentIdFilter($this->registryManager->registry('current_transaction')->getId());
        return $argument;
    }
}
