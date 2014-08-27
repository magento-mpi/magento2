<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PersistentHistory\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;

class ApplyBlockPersistentData
{
    /**
     * @var \Magento\Persistent\Model\Observer\ApplyBlockPersistentData
     */
    protected $_observer;

    /**
     * Persistent data
     *
     * @var \Magento\PersistentHistory\Helper\Data
     */
    protected $_ePersistentData = null;

    /**
     * @param \Magento\PersistentHistory\Helper\Data $ePersistentData
     * @param \Magento\Persistent\Model\Observer $observer
     */
    public function __construct(
        \Magento\PersistentHistory\Helper\Data $ePersistentData,
        \Magento\Persistent\Model\Observer\ApplyBlockPersistentData $observer
    ) {
        $this->_ePersistentData = $ePersistentData;
        $this->_observer = $observer;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute($observer)
    {
        $observer->getEvent()->setConfigFilePath($this->_ePersistentData->getPersistentConfigFilePath());
        return $this->_observer->execute($observer);
    }
}
