<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Log_Model_Shell_Command_Clean implements Mage_Log_Model_Shell_CommandInterface
{
    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Mage_Log_Model_LogFactory
     */
    protected $_logFactory;

    /**
     * Clean after days count
     *
     * @var int
     */
    protected $_days;

    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Mage_Log_Model_LogFactory $logFactory,
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
            $this->_storeManager->getStore()->setConfig(Mage_Log_Model_Log::XML_LOG_CLEAN_DAYS, $this->_days);
        }
        /** @var $model Mage_Log_Model_Log */
        $model = $this->_logFactory->create();
        $model->clean();
        return "Log cleaned\n";
    }
}