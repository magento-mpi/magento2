<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Helper;

/**
 * Eav data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * XML path to input types validator data in config
     *
     * @var string
     */
    const XML_PATH_VALIDATOR_DATA_INPUT_TYPES = 'general/validator_data/input_types';

    /**
     * @var array
     */
    protected $_attributesLockedFields = array();

    /**
     * @var array
     */
    protected $_entityTypeFrontendClasses = array();

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Config
     */
    protected $_attributeConfig;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Eav\Model\Entity\Attribute\Config $attributeConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Eav\Model\Entity\Attribute\Config $attributeConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_attributeConfig = $attributeConfig;
        $this->_eavConfig = $eavConfig;
        parent::__construct($context);
    }

    /**
     * Return default frontend classes value labal array
     *
     * @return array
     */
    protected function _getDefaultFrontendClasses()
    {
        return array(
            array('value' => '', 'label' => __('None')),
            array('value' => 'validate-number', 'label' => __('Decimal Number')),
            array('value' => 'validate-digits', 'label' => __('Integer Number')),
            array('value' => 'validate-email', 'label' => __('Email')),
            array('value' => 'validate-url', 'label' => __('URL')),
            array('value' => 'validate-alpha', 'label' => __('Letters')),
            array('value' => 'validate-alphanum', 'label' => __('Letters (a-z, A-Z) or Numbers (0-9)'))
        );
    }

    /**
     * Return merged default and entity type frontend classes value label array
     *
     * @param string $entityTypeCode
     * @return array
     */
    public function getFrontendClasses($entityTypeCode)
    {
        $_defaultClasses = $this->_getDefaultFrontendClasses();

        if (isset($this->_entityTypeFrontendClasses[$entityTypeCode])) {
            return array_merge($_defaultClasses, $this->_entityTypeFrontendClasses[$entityTypeCode]);
        }

        return $_defaultClasses;
    }

    /**
     * Retrieve attributes locked fields to edit
     *
     * @param string $entityTypeCode
     * @return array
     */
    public function getAttributeLockedFields($entityTypeCode)
    {
        if (!$entityTypeCode) {
            return array();
        }
        if (isset($this->_attributesLockedFields[$entityTypeCode])) {
            return $this->_attributesLockedFields[$entityTypeCode];
        }
        $attributesLockedFields = $this->_attributeConfig->getEntityAttributesLockedFields($entityTypeCode);
        if (count($attributesLockedFields)) {
            $this->_attributesLockedFields[$entityTypeCode] = $attributesLockedFields;
            return $this->_attributesLockedFields[$entityTypeCode];
        }
        return array();
    }

    /**
     * Get input types validator data
     *
     * @return array
     */
    public function getInputTypesValidatorData()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_VALIDATOR_DATA_INPUT_TYPES, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve attribute metadata.
     *
     * @param string $entityTypeCode
     * @param string $attributeCode
     * @return array <pre>[
     *      'entity_type_id' => $entityTypeId,
     *      'attribute_id' => $attributeId,
     *      'attribute_table' => $attributeTable
     *      'backend_type' => $backendType
     * ]</pre>
     */
    public function getAttributeMetadata($entityTypeCode, $attributeCode)
    {
        $attribute = $this->_eavConfig->getAttribute($entityTypeCode, $attributeCode);
        return array(
            'entity_type_id' => $attribute->getEntityTypeId(),
            'attribute_id' => $attribute->getAttributeId(),
            'attribute_table' => $attribute->getBackend()->getTable(),
            'backend_type' => $attribute->getBackendType()
        );
    }
}
