<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Model\Observer;

class ApplyBlockPersistentData
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Persistent config factory
     *
     * @var \Magento\Persistent\Model\Persistent\ConfigFactory
     */
    protected $_persistentConfigFactory;

    /**
     * Persistent session
     *
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSession = null;

    /**
     * Persistent data
     *
     * @var \Magento\Persistent\Helper\Data
     */
    protected $_persistentData = null;

    /**
     * @param \Magento\Persistent\Helper\Session $persistentSession
     * @param \Magento\Persistent\Helper\Data $persistentData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Persistent\Model\Persistent\ConfigFactory $persistentConfigFactory
     */
    public function __construct(
        \Magento\Persistent\Helper\Session $persistentSession,
        \Magento\Persistent\Helper\Data $persistentData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Persistent\Model\Persistent\ConfigFactory $persistentConfigFactory
    ) {
        $this->_persistentSession = $persistentSession;
        $this->_persistentData = $persistentData;
        $this->_customerSession = $customerSession;
        $this->_persistentConfigFactory = $persistentConfigFactory;
    }

    /**
     * Apply persistent data to specific block
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute($observer)
    {
        if (!$this->_persistentSession->isPersistent() || $this->_customerSession->isLoggedIn()) {
            return $this;
        }

        /** @var $block \Magento\Framework\View\Element\AbstractBlock */
        $block = $observer->getEvent()->getBlock();

        if (!$block) {
            return $this;
        }

        $configFilePath = $observer->getEvent()->getConfigFilePath();
        if (!$configFilePath) {
            $configFilePath = $this->_persistentData->getPersistentConfigFilePath();
        }

        /** @var $persistentConfig \Magento\Persistent\Model\Persistent\Config */
        $persistentConfig = $this->_persistentConfigFactory->create();
        $persistentConfig->setConfigFilePath($configFilePath);

        foreach ($persistentConfig->getBlockConfigInfo(get_class($block)) as $persistentConfigInfo) {
            $persistentConfig->fireOne($persistentConfigInfo, $block);
        }

        return $this;
    }
}
