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
 * Eav data helper
 */
namespace Magento\Eav\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * XML path to input types validator data in config
     */
    const XML_PATH_VALIDATOR_DATA_INPUT_TYPES = 'general/validator_data/input_types';

    protected $_attributesLockedFields = array();

    protected $_entityTypeFrontendClasses = array();

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Config
     */
    protected $_attributeConfig;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Eav\Model\Entity\Attribute\Config $attributeConfig
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Eav\Model\Entity\Attribute\Config $attributeConfig,
        \Magento\Core\Model\Store\Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_attributeConfig = $attributeConfig;
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
            array(
                'value' => '',
                'label' => __('None')
            ),
            array(
                'value' => 'validate-number',
                'label' => __('Decimal Number')
            ),
            array(
                'value' => 'validate-digits',
                'label' => __('Integer Number')
            ),
            array(
                'value' => 'validate-email',
                'label' => __('Email')
            ),
            array(
                'value' => 'validate-url',
                'label' => __('URL')
            ),
            array(
                'value' => 'validate-alpha',
                'label' => __('Letters')
            ),
            array(
                'value' => 'validate-alphanum',
                'label' => __('Letters (a-z, A-Z) or Numbers (0-9)')
            )
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
            return array_merge(
                $_defaultClasses,
                $this->_entityTypeFrontendClasses[$entityTypeCode]
            );
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
        return $this->_coreStoreConfig->getConfig(self::XML_PATH_VALIDATOR_DATA_INPUT_TYPES);
    }
}
