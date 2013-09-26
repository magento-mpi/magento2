<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rating grid collection
 *
 * @category    Magento
 * @package     Magento_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rating_Model_Resource_Rating_Grid_Collection extends Magento_Rating_Model_Resource_Rating_Collection
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Collection constructor
     *
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Rating_Model_Resource_Rating_Option_CollectionFactory $ratingCollectionF
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Rating_Model_Resource_Rating_Option_CollectionFactory $ratingCollectionF,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource, $data);
    }

    /**
     * Add entity filter
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract|Magento_Rating_Model_Resource_Rating_Grid_Collection
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->addEntityFilter($this->_coreRegistry->registry('entityId'));
        return $this;
    }
}
