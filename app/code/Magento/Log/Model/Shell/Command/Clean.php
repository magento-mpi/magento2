<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\Model\Shell\Command;

use Magento\Log\Model\LogFactory;

class Clean implements \Magento\Log\Model\Shell\CommandInterface
{
    /**
     * Mutable Config
     *
     * @var \Magento\App\Config\MutableScopeConfigInterface
     */
    protected $_mutableConfig;

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
     * @param \Magento\App\Config\MutableScopeConfigInterface $mutableConfig
     * @param LogFactory $logFactory
     * @param int $days
     */
    public function __construct(
        \Magento\App\Config\MutableScopeConfigInterface $mutableConfig,
        LogFactory $logFactory,
        $days
    ) {
        $this->_mutableConfig = $mutableConfig;
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
            $this->_mutableConfig->setValue(
                \Magento\Log\Model\Log::XML_LOG_CLEAN_DAYS,
                $this->_days,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        /** @var $model \Magento\Log\Model\Log */
        $model = $this->_logFactory->create();
        $model->clean();
        return "Log cleaned\n";
    }
}
