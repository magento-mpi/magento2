<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * RMA Helper
 */
class Magento_Rma_Helper_Eav extends Magento_Eav_Helper_Data
{
    /**
     * complicated array of select-typed attribute values for all stores
     *
     * @var array
     */
    protected $_attributeOptionValues = array();

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Eav_Model_Resource_Entity_Attribute_Option_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Magento_Core_Model_Resource
     */
    protected $_resource;

    /**
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Option_CollectionFactory $collectionFactory
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Eav_Model_Entity_Attribute_Config $attributeConfig
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Eav_Model_Resource_Entity_Attribute_Option_CollectionFactory $collectionFactory,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Helper_Context $context,
        Magento_Eav_Model_Entity_Attribute_Config $attributeConfig,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_storeManager = $storeManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_resource = $resource;
        parent::__construct($context, $attributeConfig, $coreStoreConfig);
    }

    /**
     * Default attribute entity type code
     *
     * @return string
     */
    protected function _getEntityTypeCode()
    {
        return 'rma_item';
    }

    /**
     * Return data array of RMA item attribute Input Types
     *
     * @param string|null $inputType
     * @return array
     */
    public function getAttributeInputTypes($inputType = null)
    {
        $inputTypes = array(
            'text' => array(
                'label'             => __('Text Field'),
                'manage_options'    => false,
                'validate_types'    => array(
                    'min_text_length',
                    'max_text_length',
                ),
                'validate_filters'  => array(
                    'alphanumeric',
                    'numeric',
                    'alpha',
                    'url',
                    'email',
                ),
                'filter_types'      => array(
                    'striptags',
                    'escapehtml'
                ),
                'backend_type'      => 'varchar',
                'default_value'     => 'text',
            ),
            'textarea' => array(
                'label'             => __('Text Area'),
                'manage_options'    => false,
                'validate_types'    => array(
                    'min_text_length',
                    'max_text_length',
                ),
                'validate_filters'  => array(),
                'filter_types'      => array(
                    'striptags',
                    'escapehtml'
                ),
                'backend_type'      => 'text',
                'default_value'     => 'textarea',
            ),
            'select' => array(
                'label'             => __('Dropdown'),
                'manage_options'    => true,
                'option_default'    => 'radio',
                'validate_types'    => array(),
                'validate_filters'  => array(),
                'filter_types'      => array(),
                'source_model'      => 'Magento_Eav_Model_Entity_Attribute_Source_Table',
                'backend_type'      => 'int',
                'default_value'     => false,
            ),
            'image' => array(
                'label'             => __('Image File'),
                'manage_options'    => false,
                'validate_types'    => array(
                    'max_file_size',
                    'max_image_width',
                    'max_image_heght',
                ),
                'validate_filters'  => array(),
                'filter_types'      => array(),
                'backend_type'      => 'varchar',
                'default_value'     => false,
            ),
        );

        if (is_null($inputType)) {
            return $inputTypes;
        } else if (isset($inputTypes[$inputType])) {
            return $inputTypes[$inputType];
        }
        return array();
    }

    /**
     * Get array of select-typed attribute values depending by store
     *
     * Uses internal protected method, which must use data from protected variable
     *
     * @param null|int|Magento_Core_Model_Store $storeId
     * @param bool $useDefaultValue
     * @return array
     */
    public function getAttributeOptionStringValues($storeId = null, $useDefaultValue = true)
    {
        $values = $this->_getAttributeOptionValues($storeId, $useDefaultValue);
        $return = array();
        foreach ($values as $temValue) {
            foreach ($temValue as $value) {
                $return[$value['option_id']] = $value['value'];
            }
        }
        return $return;
    }

    /**
     * Get array of key=>value pair for passed attribute code depending by store
     *
     * Uses internal protected method, which must use data from protected variable
     *
     * @param string $attributeCode
     * @param null|int|Magento_Core_Model_Store $storeId
     * @param bool $useDefaultValue
     * @return array
     */
    public function getAttributeOptionValues($attributeCode, $storeId = null, $useDefaultValue = true)
    {
        $values = $this->_getAttributeOptionValues($storeId, $useDefaultValue);
        $return = array();
        if (isset($values[$attributeCode])) {
            foreach ($values[$attributeCode] as $key => $value) {
                $return[$key] = $value['value'];
            }
        }
        return $return;
    }

    /**
     * Get complicated array of select-typed attribute values depending by store
     *
     * @param null|int|Magento_Core_Model_Store $storeId
     * @param bool $useDefaultValue
     * @return array
     */
    protected function _getAttributeOptionValues($storeId = null, $useDefaultValue = true)
    {
        if (is_null($storeId)) {
            $storeId = $this->_storeManager->getStore()->getId();
        } elseif ($storeId instanceof Magento_Core_Model_Store) {
            $storeId = $storeId->getId();
        }

        if (!isset($this->_attributeOptionValues[$storeId])) {
            $optionCollection = $this->_collectionFactory->create()->setStoreFilter($storeId, $useDefaultValue);
            $optionCollection->getSelect()
                ->join(
                    array('ea' => $this->_resource->getTableName('eav_attribute')),
                    'main_table.attribute_id = ea.attribute_id',
                    array('attribute_code' => 'ea.attribute_code'))
                ->join(
                    array('eat' => $this->_resource->getTableName('eav_entity_type')),
                    'ea.entity_type_id = eat.entity_type_id',
                    array(''))
                ->where('eat.entity_type_code = ?', $this->_getEntityTypeCode());
            $value = array();
            foreach ($optionCollection as $option) {
                $value[$option->getAttributeCode()][$option->getOptionId()] = $option->getData();
            }
            $this->_attributeOptionValues[$storeId] = $value;
        }
        return $this->_attributeOptionValues[$storeId];
    }

    /**
     * Retrieve additional style classes for text-based RMA attributes (represented by text input or textarea)
     *
     * @param Magento_Object $attribute
     * @return array
     */
    public function getAdditionalTextElementClasses(Magento_Object $attribute)
    {
        $additionalClasses = array();

        $validateRules = $attribute->getValidateRules();
        if (!empty($validateRules['min_text_length'])) {
            $additionalClasses[] = 'validate-length';
            $additionalClasses[] = 'minimum-length-' . $validateRules['min_text_length'];
        }
        if (!empty($validateRules['max_text_length'])) {
            if (!in_array('validate-length', $additionalClasses)) {
                $additionalClasses[] = 'validate-length';
            }
            $additionalClasses[] = 'maximum-length-' . $validateRules['max_text_length'];
        }

        return $additionalClasses;
    }
}
