<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Collection Advanced
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogSearch_Model_Resource_Advanced_Collection extends Magento_Catalog_Model_Resource_Product_Collection
{
    /**
     * Date
     *
     * @var Magento_Core_Model_Date
     */
    protected $_date;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Core_Model_Resource $coreResource
     * @param Magento_Eav_Model_EntityFactory $eavEntityFactory
     * @param Magento_Validator_UniversalFactory $universalFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Catalog_Helper_Product_Flat $catalogProductFlat
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Catalog_Model_Product_OptionFactory $productOptionFactory
     * @param Magento_Catalog_Model_Resource_Url $catalogUrl
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Catalog_Model_Resource_Helper $resourceHelper
     * @param Magento_Core_Model_Date $date
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Core_Model_Resource $coreResource,
        Magento_Eav_Model_EntityFactory $eavEntityFactory,
        Magento_Validator_UniversalFactory $universalFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Catalog_Helper_Product_Flat $catalogProductFlat,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Catalog_Model_Product_OptionFactory $productOptionFactory,
        Magento_Catalog_Model_Resource_Url $catalogUrl,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Customer_Model_Session $customerSession,
        Magento_Catalog_Model_Resource_Helper $resourceHelper,
        Magento_Core_Model_Date $date
    )
    {
        $this->_date = $date;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $eavConfig,
            $coreResource, $eavEntityFactory, $universalFactory, $storeManager, $catalogData,
            $catalogProductFlat, $coreStoreConfig, $productOptionFactory, $catalogUrl, $locale,
            $customerSession, $resourceHelper
        );
    }

    /**
     * Add not indexable fields to search
     *
     * @param array $fields
     * @return Magento_CatalogSearch_Model_Resource_Advanced_Collection
     * @throws Magento_Core_Exception
     */
    public function addFieldsToFilter($fields)
    {
        if ($fields) {
            $previousSelect = null;
            $conn = $this->getConnection();
            foreach ($fields as $table => $conditions) {
                foreach ($conditions as $attributeId => $conditionValue) {
                    $select = $conn->select();
                    $select->from(array('t1' => $table), 'entity_id');
                    $conditionData = array();

                    if (!is_numeric($attributeId)) {
                        $field = 't1.'.$attributeId;
                    }
                    else {
                        $storeId = $this->getStoreId();
                        $onCondition = 't1.entity_id = t2.entity_id'
                                . ' AND t1.attribute_id = t2.attribute_id'
                                . ' AND t2.store_id=?';

                        $select->joinLeft(
                            array('t2' => $table),
                            $conn->quoteInto($onCondition, $storeId),
                            array()
                        );
                        $select->where('t1.store_id = ?', 0);
                        $select->where('t1.attribute_id = ?', $attributeId);

                        if (array_key_exists('price_index', $this->getSelect()->getPart(Magento_DB_Select::FROM))) {
                            $select->where('t1.entity_id = price_index.entity_id');
                        }

                        $field = $this->getConnection()->getCheckSql('t2.value_id>0', 't2.value', 't1.value');

                    }

                    if (is_array($conditionValue)) {
                        if (isset($conditionValue['in'])){
                            $conditionData[] = array('in' => $conditionValue['in']);
                        }
                        elseif (isset($conditionValue['in_set'])) {
                            $conditionParts = array();
                            foreach ($conditionValue['in_set'] as $value) {
                                $conditionParts[] = array('finset' => $value);
                            }
                            $conditionData[] = $conditionParts;
                        }
                        elseif (isset($conditionValue['like'])) {
                            $conditionData[] = array ('like' => $conditionValue['like']);
                        }
                        elseif (isset($conditionValue['from']) && isset($conditionValue['to'])) {
                            $invalidDateMessage = __('Please specify correct data.');
                            if ($conditionValue['from']) {
                                if (!Zend_Date::isDate($conditionValue['from'])) {
                                    throw new Magento_Core_Exception($invalidDateMessage);
                                }
                                if (!is_numeric($conditionValue['from'])){
                                    $conditionValue['from'] = $this->_date->gmtDate(null, $conditionValue['from']);
                                    if (!$conditionValue['from']) {
                                        $conditionValue['from'] = $this->_date->gmtDate();
                                    }
                                }
                                $conditionData[] = array('gteq' => $conditionValue['from']);
                            }
                            if ($conditionValue['to']) {
                                if (!Zend_Date::isDate($conditionValue['to'])) {
                                    throw new Magento_Core_Exception($invalidDateMessage);
                                }
                                if (!is_numeric($conditionValue['to'])){
                                    $conditionValue['to'] = $this->_date->gmtDate(null, $conditionValue['to']);
                                    if (!$conditionValue['to']) {
                                        $conditionValue['to'] = $this->_date->gmtDate();
                                    }
                                }
                                $conditionData[] = array('lteq' => $conditionValue['to']);
                            }
                        }
                    } else {
                        $conditionData[] = array('eq' => $conditionValue);
                    }


                    foreach ($conditionData as $data) {
                        $select->where($conn->prepareSqlCondition($field, $data));
                    }

                    if (!is_null($previousSelect)) {
                        $select->where('t1.entity_id IN (?)', new Zend_Db_Expr($previousSelect));
                    }
                    $previousSelect = $select;
                }
            }
            $this->addFieldToFilter('entity_id', array('in' => new Zend_Db_Expr($select)));
        }

        return $this;
    }
}
