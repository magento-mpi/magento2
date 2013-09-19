<?php
/**
 * Log Online visitors collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Log_Model_Resource_Visitor_Online_Grid_Collection extends Magento_Log_Model_Resource_Visitor_Online_Collection
{
    /**
     * @var Magento_Log_Model_Visitor_OnlineFactory
     */
    protected $_onlineFactory;

    /**
     * @param Magento_Log_Model_Visitor_OnlineFactory $onlineFactory
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Log_Model_Visitor_OnlineFactory $onlineFactory,
        Magento_Customer_Model_CustomerFactory $customerFactory,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        $this->_onlineFactory = $onlineFactory;
        parent::__construct($customerFactory, $eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
    }

    /**
     * @return Magento_Log_Model_Resource_Visitor_Online_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_onlineFactory->create()->prepare();
        $this->addCustomerData();
        return $this;
    }

}
