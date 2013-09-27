<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV Form Attribute Resource Collection
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Resource_Form_Attribute_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Current module pathname
     *
     * @var string
     */
    protected $_moduleName = '';

    /**
     * Current EAV entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = '';

    /**
     * Current store instance
     *
     * @var Magento_Core_Model_Store
     */
    protected $_store;

    /**
     * Eav Entity Type instance
     *
     * @var Magento_Eav_Model_Entity_Type
     */
    protected $_entityType;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        $this->_storeManager = $storeManager;
        $this->_eavConfig = $eavConfig;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
    }

    /**
     * Resource initialization
     *
     * @throws Magento_Core_Exception
     */
    protected function _construct()
    {
        if (empty($this->_moduleName)) {
            throw new Magento_Core_Exception(__('Current module pathname is undefined'));
        }
        if (empty($this->_entityTypeCode)) {
            throw new Magento_Core_Exception(__('Current module EAV entity is undefined'));
        }
    }

    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    protected function _getEavWebsiteTable()
    {
        return null;
    }

    /**
     * Set current store to collection
     *
     * @param Magento_Core_Model_Store|string|int $store
     * @return Magento_Eav_Model_Resource_Form_Attribute_Collection
     */
    public function setStore($store)
    {
        $this->_store = $this->_storeManager->getStore($store);
        return $this;
    }

    /**
     * Return current store instance
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->_store === null) {
            $this->_store = $this->_storeManager->getStore();
        }
        return $this->_store;
    }

    /**
     * Set entity type instance to collection
     *
     * @param Magento_Eav_Model_Entity_Type|string|int $entityType
     * @return Magento_Eav_Model_Resource_Form_Attribute_Collection
     */
    public function setEntityType($entityType)
    {
        $this->_entityType = $this->_eavConfig->getEntityType($entityType);
        return $this;
    }

    /**
     * Return current entity type instance
     *
     * @return Magento_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {
        if ($this->_entityType === null) {
            $this->setEntityType($this->_entityTypeCode);
        }
        return $this->_entityType;
    }

    /**
     * Add Form Code filter to collection
     *
     * @param string $code
     * @return Magento_Eav_Model_Resource_Form_Attribute_Collection
     */
    public function addFormCodeFilter($code)
    {
        return $this->addFieldToFilter('main_table.form_code', $code);
    }

    /**
     * Set order by attribute sort order
     *
     * @param string $direction
     * @return Magento_Eav_Model_Resource_Form_Attribute_Collection
     */
    public function setSortOrder($direction = self::SORT_ORDER_ASC)
    {
        $this->setOrder('ea.is_user_defined', self::SORT_ORDER_ASC);
        return $this->setOrder('ca.sort_order', $direction);
    }

    /**
     * Add joins to select
     *
     * @return Magento_Eav_Model_Resource_Form_Attribute_Collection
     */
    protected function _beforeLoad()
    {
        $select     = $this->getSelect();
        $connection = $this->getConnection();
        $entityType = $this->getEntityType();
        $this->setItemObjectClass($entityType->getAttributeModel());

        $eaColumns  = array();
        $caColumns  = array();
        $saColumns  = array();

        $eaDescribe = $connection->describeTable($this->getTable('eav_attribute'));
        unset($eaDescribe['attribute_id']);
        foreach (array_keys($eaDescribe) as $columnName) {
            $eaColumns[$columnName] = $columnName;
        }

        $select->join(
            array('ea' => $this->getTable('eav_attribute')),
            'main_table.attribute_id = ea.attribute_id',
            $eaColumns
        );

        // join additional attribute data table
        $additionalTable = $entityType->getAdditionalAttributeTable();
        if ($additionalTable) {
            $caDescribe = $connection->describeTable($this->getTable($additionalTable));
            unset($caDescribe['attribute_id']);
            foreach (array_keys($caDescribe) as $columnName) {
                $caColumns[$columnName] = $columnName;
            }

            $select->join(
                array('ca' => $this->getTable($additionalTable)),
                'main_table.attribute_id = ca.attribute_id',
                $caColumns
            );
        }

        // add scope values
        if ($this->_getEavWebsiteTable()) {
            $saDescribe = $connection->describeTable($this->_getEavWebsiteTable());
            unset($saDescribe['attribute_id']);
            foreach (array_keys($saDescribe) as $columnName) {
                if ($columnName == 'website_id') {
                    $saColumns['scope_website_id'] = $columnName;
                } else {
                    if (isset($eaColumns[$columnName])) {
                        $code = sprintf('scope_%s', $columnName);
                        $expression = $connection->getCheckSql('sa.%s IS NULL', 'ea.%s', 'sa.%s');
                        $saColumns[$code] = new Zend_Db_Expr(sprintf($expression,
                            $columnName, $columnName, $columnName));
                    } elseif (isset($caColumns[$columnName])) {
                        $code = sprintf('scope_%s', $columnName);
                        $expression = $connection->getCheckSql('sa.%s IS NULL', 'ca.%s', 'sa.%s');
                        $saColumns[$code] = new Zend_Db_Expr(sprintf($expression,
                            $columnName, $columnName, $columnName));
                    }
                }
            }

            $store = $this->getStore();
            $joinWebsiteExpression = $connection
                ->quoteInto(
                    'sa.attribute_id = main_table.attribute_id AND sa.website_id = ?', (int)$store->getWebsiteId()
                );
            $select->joinLeft(
                array('sa' => $this->_getEavWebsiteTable()),
                $joinWebsiteExpression,
                $saColumns
            );
        }


        // add store attribute label
        if ($store->isAdmin()) {
            $select->columns(array('store_label' => 'ea.frontend_label'));
        } else {
            $storeLabelExpr = $connection->getCheckSql('al.value IS NULL', 'ea.frontend_label', 'al.value');
            $joinExpression = $connection
                ->quoteInto('al.attribute_id = main_table.attribute_id AND al.store_id = ?', (int)$store->getId());
            $select->joinLeft(
                array('al' => $this->getTable('eav_attribute_label')),
                $joinExpression,
                array('store_label' => $storeLabelExpr)
            );
        }

        // add entity type filter
        $select->where('ea.entity_type_id = ?', (int)$entityType->getId());

        return parent::_beforeLoad();
    }
}
