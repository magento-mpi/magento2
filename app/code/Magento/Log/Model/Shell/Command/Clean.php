<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\Model\Shell\Command;

use Magento\Core\Model\StoreManagerInterface;
use Magento\Log\Model\LogFactory;

class Clean implements \Magento\Log\Model\Shell\CommandInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var LogFactory
     */
    protected $_logFactory;

    /**
     * Clean after days count
     *
     * @var int
     */
    protected $_days;

    /**
     * @param StoreManagerInterface $storeManager
     * @param LogFactory $logFactory
     * @param int $days
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        LogFactory $logFactory,
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
