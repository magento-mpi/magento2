<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog category EAV additional attribute resource collection
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Category_Attribute_Collection
    extends Magento_Eav_Model_Resource_Entity_Attribute_Collection
{
    /**
     * Entity factory
     *
     * @var Magento_Eav_Model_EntityFactory
     */
    protected $_eavEntityFactory;

    /**
     * Construct
     *
     * @param Magento_Eav_Model_EntityFactory $eavEntityFactory
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Eav_Model_EntityFactory $eavEntityFactory,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        $this->_eavEntityFactory = $eavEntityFactory;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
    }

    /**
     * Main select object initialization.
     * Joins catalog/eav_attribute table
     *
     * @return Magento_Catalog_Model_Resource_Category_Attribute_Collection
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(array('main_table' => $this->getResource()->getMainTable()))
            ->where(
                'main_table.entity_type_id=?',
                $this->_eavEntityFactory->create()->setType(Magento_Catalog_Model_Category::ENTITY)->getTypeId()
            )->join(
                array('additional_table' => $this->getTable('catalog_eav_attribute')),
                'additional_table.attribute_id = main_table.attribute_id'
            );
        return $this;
    }

    /**
     * Specify attribute entity type filter
     *
     * @param int $typeId
     * @return Magento_Catalog_Model_Resource_Category_Attribute_Collection
     */
    public function setEntityTypeFilter($typeId)
    {
        return $this;
    }
}
