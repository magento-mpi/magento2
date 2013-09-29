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
 * EAV Entity attribute model
 *
 * @method Magento_Eav_Model_Entity_Attribute setOption($value)
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Entity_Attribute extends Magento_Eav_Model_Entity_Attribute_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix                         = 'eav_entity_attribute';

    CONST ATTRIBUTE_CODE_MAX_LENGTH                 = 30;

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getAttribute() in this case
     *
     * @var string
     */
    protected $_eventObject = 'attribute';

    const CACHE_TAG         = 'EAV_ATTRIBUTE';
    protected $_cacheTag    = 'EAV_ATTRIBUTE';

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_catalogProductFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Eav_Model_Entity_TypeFactory $eavTypeFactory
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Eav_Model_Resource_Helper $resourceHelper
     * @param Magento_Validator_UniversalFactory $universalFactory
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Catalog_Model_ProductFactory $catalogProductFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Helper_Data $coreData,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Eav_Model_Entity_TypeFactory $eavTypeFactory,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Eav_Model_Resource_Helper $resourceHelper,
        Magento_Validator_UniversalFactory $universalFactory,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Catalog_Model_ProductFactory $catalogProductFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $coreData,
            $eavConfig,
            $eavTypeFactory,
            $storeManager,
            $resourceHelper,
            $universalFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_locale = $locale;
        $this->_catalogProductFactory = $catalogProductFactory;
    }

    /**
     * Retrieve default attribute backend model by attribute code
     *
     * @return string
     */
    protected function _getDefaultBackendModel()
    {
        switch ($this->getAttributeCode()) {
            case 'created_at':
                return 'Magento_Eav_Model_Entity_Attribute_Backend_Time_Created';

            case 'updated_at':
                return 'Magento_Eav_Model_Entity_Attribute_Backend_Time_Updated';

            case 'store_id':
                return 'Magento_Eav_Model_Entity_Attribute_Backend_Store';

            case 'increment_id':
                return 'Magento_Eav_Model_Entity_Attribute_Backend_Increment';
        }

        return parent::_getDefaultBackendModel();
    }

    /**
     * Retrieve default attribute frontend model
     *
     * @return string
     */
    protected function _getDefaultFrontendModel()
    {
        return parent::_getDefaultFrontendModel();
    }

    /**
     * Retrieve default attribute source model
     *
     * @return string
     */
    protected function _getDefaultSourceModel()
    {
        if ($this->getAttributeCode() == 'store_id') {
            return 'Magento_Eav_Model_Entity_Attribute_Source_Store';
        }
        return parent::_getDefaultSourceModel();
    }

    /**
     * Delete entity
     *
     * @return Magento_Eav_Model_Resource_Entity_Attribute
     */
    public function deleteEntity()
    {
        return $this->_getResource()->deleteEntity($this);
    }

    /**
     * Load entity_attribute_id into $this by $this->attribute_set_id
     *
     * @return Magento_Core_Model_Abstract
     */
    public function loadEntityAttributeIdBySet()
    {
        // load attributes collection filtered by attribute_id and attribute_set_id
        $filteredAttributes = $this->getResourceCollection()
            ->setAttributeSetFilter($this->getAttributeSetId())
            ->addFieldToFilter('entity_attribute.attribute_id', $this->getId())
            ->load();
        if (count($filteredAttributes) > 0) {
            // getFirstItem() can be used as we can have one or zero records in the collection
            $this->setEntityAttributeId($filteredAttributes->getFirstItem()->getEntityAttributeId());
        }
        return $this;
    }

    /**
     * Prepare data for save
     *
     * @return Magento_Eav_Model_Entity_Attribute
     */
    protected function _beforeSave()
    {
        // prevent overriding product data
        if (isset($this->_data['attribute_code'])
            && $this->_catalogProductFactory->create()->isReservedAttribute($this)
        ) {
            throw new Magento_Eav_Exception(__('The attribute code \'%1\' is reserved by system. Please try another attribute code', $this->_data['attribute_code']));
        }

        /**
         * Check for maximum attribute_code length
         */
        if(isset($this->_data['attribute_code']) &&
           !Zend_Validate::is($this->_data['attribute_code'],
                              'StringLength',
                              array('max' => self::ATTRIBUTE_CODE_MAX_LENGTH))
        ) {
            throw new Magento_Eav_Exception(__('Maximum length of attribute code must be less than %1 symbols', self::ATTRIBUTE_CODE_MAX_LENGTH));
        }

        $defaultValue   = $this->getDefaultValue();
        $hasDefaultValue = ((string)$defaultValue != '');

        if ($this->getBackendType() == 'decimal' && $hasDefaultValue) {
            if (!Zend_Locale_Format::isNumber($defaultValue,
                                              array('locale' => $this->_locale->getLocaleCode()))
            ) {
                throw new Magento_Eav_Exception(__('Invalid default decimal value'));
            }

            try {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => $this->_locale->getLocaleCode())
                );
                $this->setDefaultValue($filter->filter($defaultValue));
            } catch (Exception $e) {
                throw new Magento_Eav_Exception(__('Invalid default decimal value'));
            }
        }

        if ($this->getBackendType() == 'datetime') {
            if (!$this->getBackendModel()) {
                $this->setBackendModel('Magento_Eav_Model_Entity_Attribute_Backend_Datetime');
            }

            if (!$this->getFrontendModel()) {
                $this->setFrontendModel('Magento_Eav_Model_Entity_Attribute_Frontend_Datetime');
            }

            // save default date value as timestamp
            if ($hasDefaultValue) {
                $format = $this->_locale->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
                try {
                    $defaultValue = $this->_locale->date($defaultValue, $format, null, false)->toValue();
                    $this->setDefaultValue($defaultValue);
                } catch (Exception $e) {
                    throw new Magento_Eav_Exception(__('Invalid default date'));
                }
            }
        }

        if ($this->getBackendType() == 'gallery') {
            if (!$this->getBackendModel()) {
                $this->setBackendModel('Magento_Eav_Model_Entity_Attribute_Backend_Default');
            }
        }

        return parent::_beforeSave();
    }

    /**
     * Save additional data
     *
     * @return Magento_Eav_Model_Entity_Attribute
     */
    protected function _afterSave()
    {
        $this->_getResource()->saveInSetIncluding($this);
        return parent::_afterSave();
    }

    /**
     * Detect backend storage type using frontend input type
     *
     * @param string $type frontend_input field value
     * @return string backend_type field value
     */
    public function getBackendTypeByInput($type)
    {
        $field = null;
        switch ($type) {
            case 'text':
            case 'gallery':
            case 'media_image':
            case 'multiselect':
                $field = 'varchar';
                break;

            case 'image':
            case 'textarea':
                $field = 'text';
                break;

            case 'date':
                $field = 'datetime';
                break;

            case 'select':
            case 'boolean':
                $field = 'int';
                break;

            case 'price':
            case 'weight':
                $field = 'decimal';
                break;
        }

        return $field;
    }

    /**
     * Detect default value using frontend input type
     *
     * @param string $type frontend_input field name
     * @return string default_value field value
     */
    public function getDefaultValueByInput($type)
    {
        $field = '';
        switch ($type) {
            case 'select':
            case 'gallery':
            case 'media_image':
                break;
            case 'multiselect':
                $field = null;
                break;

            case 'text':
            case 'price':
            case 'image':
            case 'weight':
                $field = 'default_value_text';
                break;

            case 'textarea':
                $field = 'default_value_textarea';
                break;

            case 'date':
                $field = 'default_value_date';
                break;

            case 'boolean':
                $field = 'default_value_yesno';
                break;
        }

        return $field;
    }

    /**
     * Retrieve attribute codes by frontend type
     *
     * @param string $type
     * @return array
     */
    public function getAttributeCodesByFrontendType($type)
    {
        return $this->getResource()->getAttributeCodesByFrontendType($type);
    }

    /**
     * Return array of labels of stores
     *
     * @return array
     */
    public function getStoreLabels()
    {
        if (!$this->getData('store_labels')) {
            $storeLabel = $this->getResource()->getStoreLabelsByAttributeId($this->getId());
            $this->setData('store_labels', $storeLabel);
        }
        return $this->getData('store_labels');
    }

    /**
     * Return store label of attribute
     *
     * @return string
     */
    public function getStoreLabel($storeId = null)
    {
        if ($this->hasData('store_label')) {
            return $this->getData('store_label');
        }
        $store = $this->_storeManager->getStore($storeId);
        $label = false;
        if (!$store->isAdmin()) {
            $labels = $this->getStoreLabels();
            if (isset($labels[$store->getId()])) {
                return $labels[$store->getId()];
            }
        }
        return $this->getFrontendLabel();
    }

    /**
     * Get attribute sort weight
     *
     * @param int $setId
     * @return float
     */
    public function getSortWeight($setId)
    {
        $groupSortWeight = isset($this->_data['attribute_set_info'][$setId]['group_sort'])
            ? (float) $this->_data['attribute_set_info'][$setId]['group_sort'] * 1000
            : 0.0;
        $sortWeight = isset($this->_data['attribute_set_info'][$setId]['sort'])
            ? (float)$this->_data['attribute_set_info'][$setId]['sort'] * 0.0001
            : 0.0;
        return $groupSortWeight + $sortWeight;
    }
}
