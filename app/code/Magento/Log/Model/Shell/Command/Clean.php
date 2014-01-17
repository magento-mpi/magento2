<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\Model\Shell\Command;

class Clean implements \Magento\Log\Model\Shell\CommandInterface
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Log\Model\LogFactory
     */
    protected $_logFactory;

    /**
     * Clean after days count
     *
     * @var int
     */
    protected $_days;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Log\Model\LogFactory $logFactory
     * @param int $days
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Log\Model\LogFactory $logFactory,
        $days
    ) {
        $this->_storeManager = $storeManager;
        $this->_logFactory = $logFactory;
        $this->_days = $days;
    }

    /**
     * Execute command
     *
     * @return string
     */
    public function execute()
    {
        if ($this->_days > 0) {
            $this->_storeManager->getStore()->setConfig(\Magento\Log\Model\Log::XML_LOG_CLEAN_DAYS, $this->_days);
        }
        /** @var $model \Magento\Log\Model\Log */
        $model = $this->_logFactory->create();
        $model->clean();
        return "Log cleaned\n";
    }
}
