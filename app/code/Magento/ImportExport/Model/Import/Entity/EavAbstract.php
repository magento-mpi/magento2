<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import EAV entity abstract model
 */
abstract class Magento_ImportExport_Model_Import_Entity_EavAbstract
    extends Magento_ImportExport_Model_Import_EntityAbstract
{
    /**
     * Website manager (currently Magento_Core_Model_App works as website manager)
     *
     * @var Magento_Core_Model_App
     */
    protected $_websiteManager;

    /**
     * Store manager (currently Magento_Core_Model_App works as store manager)
     *
     * @var Magento_Core_Model_App
     */
    protected $_storeManager;

    /**
     * Entity type id
     *
     * @var int
     */
    protected $_entityTypeId;

    /**
     * Attributes with index (not label) value
     *
     * @var array
     */
    protected $_indexValueAttributes = array();

    /**
     * Website code-to-ID
     *
     * @var array
     */
    protected $_websiteCodeToId = array();

    /**
     * All stores code-ID pairs.
     *
     * @var array
     */
    protected $_storeCodeToId = array();

    /**
     * Entity attributes parameters
     *
     *  [attr_code_1] => array(
     *      'options' => array(),
     *      'type' => 'text', 'price', 'textarea', 'select', etc.
     *      'id' => ..
     *  ),
     *  ...
     *
     * @var array
     */
    protected $_attributes = array();

    /**
     * Attributes collection
     *
     * @var Magento_Data_Collection
     */
    protected $_attributeCollection;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Helper_String $coreString
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_ImportExport_Model_ImportFactory $importFactory
     * @param Magento_ImportExport_Model_Resource_Helper_Mysql4 $resourceHelper
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_App $app
     * @param Magento_Data_CollectionFactory $collectionFactory
     * @param Magento_Eav_Model_Config $eavConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Helper_String $coreString,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_ImportExport_Model_ImportFactory $importFactory,
        Magento_ImportExport_Model_Resource_Helper_Mysql4 $resourceHelper,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_App $app,
        Magento_Data_CollectionFactory $collectionFactory,
        Magento_Eav_Model_Config $eavConfig,
        array $data = array()
    ) {
        parent::__construct(
            $coreData, $coreString, $coreStoreConfig, $importFactory, $resourceHelper, $resource, $data
        );

        $this->_websiteManager = isset($data['website_manager']) ? $data['website_manager'] : $app;
        $this->_storeManager   = isset($data['store_manager']) ? $data['store_manager'] : $app;
        $this->_attributeCollection = isset($data['attribute_collection']) ? $data['attribute_collection']
            : $collectionFactory->create();

        if (isset($data['entity_type_id'])) {
            $this->_entityTypeId = $data['entity_type_id'];
        } else {
            $this->_entityTypeId = $eavConfig->getEntityType($this->getEntityTypeCode())->getEntityTypeId();
        }
    }

    /**
     * Retrieve website id by code or false when website code not exists
     *
     * @param $websiteCode
     * @return bool|int
     */
    public function getWebsiteId($websiteCode)
    {
        if (isset($this->_websiteCodeToId[$websiteCode])) {
            return $this->_websiteCodeToId[$websiteCode];
        }

        return false;
    }

    /**
     * Initialize website values
     *
     * @param bool $withDefault
     * @return Magento_ImportExport_Model_Import_Entity_EavAbstract
     */
    protected function _initWebsites($withDefault = false)
    {
        /** @var $website Magento_Core_Model_Website */
        foreach ($this->_websiteManager->getWebsites($withDefault) as $website) {
            $this->_websiteCodeToId[$website->getCode()] = $website->getId();
        }
        return $this;
    }

    /**
     * Initialize stores data
     *
     * @param bool $withDefault
     * @return Magento_ImportExport_Model_Import_Entity_EavAbstract
     */
    protected function _initStores($withDefault = false)
    {
        /** @var $store Magento_Core_Model_Store */
        foreach ($this->_storeManager->getStores($withDefault) as $store) {
            $this->_storeCodeToId[$store->getCode()] = $store->getId();
        }
        return $this;
    }

    /**
     * Initialize entity attributes
     *
     * @return Magento_ImportExport_Model_Import_Entity_EavAbstract
     */
    protected function _initAttributes()
    {
        /** @var $attribute Magento_Eav_Model_Attribute */
        foreach ($this->_attributeCollection as $attribute) {
            $this->_attributes[$attribute->getAttributeCode()] = array(
                'id'          => $attribute->getId(),
                'code'        => $attribute->getAttributeCode(),
                'table'       => $attribute->getBackend()->getTable(),
                'is_required' => $attribute->getIsRequired(),
                'is_static'   => $attribute->isStatic(),
                'rules'       => $attribute->getValidateRules() ? unserialize($attribute->getValidateRules()) : null,
                'type'        => Magento_ImportExport_Model_Import::getAttributeType($attribute),
                'options'     => $this->getAttributeOptions($attribute)
            );
        }
        return $this;
    }

    /**
     * Entity type ID getter
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        return $this->_entityTypeId;
    }

    /**
     * Returns attributes all values in label-value or value-value pairs form. Labels are lower-cased
     *
     * @param Magento_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param array $indexAttributes OPTIONAL Additional attribute codes with index values.
     * @return array
     */
    public function getAttributeOptions(Magento_Eav_Model_Entity_Attribute_Abstract $attribute,
        array $indexAttributes = array()
    ) {
        $options = array();

        if ($attribute->usesSource()) {
            // merge global entity index value attributes
            $indexAttributes = array_merge($indexAttributes, $this->_indexValueAttributes);

            // should attribute has index (option value) instead of a label?
            $index = in_array($attribute->getAttributeCode(), $indexAttributes) ? 'value' : 'label';

            // only default (admin) store values used
            $attribute->setStoreId(Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID);

            try {
                foreach ($attribute->getSource()->getAllOptions(false) as $option) {
                    $value = is_array($option['value']) ? $option['value'] : array($option);
                    foreach ($value as $innerOption) {
                        if (strlen($innerOption['value'])) { // skip ' -- Please Select -- ' option
                            $options[strtolower($innerOption[$index])] = $innerOption['value'];
                        }
                    }
                }
            } catch (Exception $e) {
                // ignore exceptions connected with source models
            }
        }
        return $options;
    }

    /**
     * Get attribute collection
     *
     * @return Magento_Data_Collection
     */
    public function getAttributeCollection()
    {
        return $this->_attributeCollection;
    }
}
