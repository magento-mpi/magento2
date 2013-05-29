<?php
/**
 * Google AdWords Data Helper
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleAdwords_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Google AdWords language codes
     */
    const XML_PATH_LANGUAGES = 'default/google/adwords/languages';

    /**
     * Google AdWords registry name for conversion value
     */
    const CONVERSION_VALUE_REGISTRY_NAME = 'google_adwords_conversion_value';

    /**
     * Default value for conversion value
     */
    CONST CONVERSION_VALUE_DEFAULT = 0;

    /**#@+
     * Google AdWords config data
     */
    const XML_PATH_ACTIVE  = 'google/adwords/active';
    const XML_PATH_CONVERSION_ID = 'google/adwords/conversion_id';
    const XML_PATH_CONVERSION_LANGUAGE = 'google/adwords/conversion_language';
    const XML_PATH_CONVERSION_FORMAT = 'google/adwords/conversion_format';
    const XML_PATH_CONVERSION_COLOR = 'google/adwords/conversion_color';
    const XML_PATH_CONVERSION_LABEL = 'google/adwords/conversion_label';
    const XML_PATH_CONVERSION_VALUE_TYPE = 'google/adwords/conversion_value_type';
    const XML_PATH_CONVERSION_VALUE = 'google/adwords/conversion_value';
    /**#@-*/

    /**#@+
     * Conversion value types
     */
    CONST CONVERSION_VALUE_TYPE_DYNAMIC = 1;
    CONST CONVERSION_VALUE_TYPE_CONSTANT = 0;
    /**#@-*/

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Mage_Core_Model_Store_ConfigInterface
     */
    protected $_storeConfig;

    /**
     * @var Mage_Core_Model_Registry
     */
    protected $_registry;

    /**
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Model_Config $config
     * @param Mage_Core_Model_Store_ConfigInterface $storeConfig
     * @param Mage_Core_Model_Registry $registry
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Mage_Core_Model_Config $config,
        Mage_Core_Model_Store_ConfigInterface $storeConfig,
        Mage_Core_Model_Registry $registry
    ) {
        parent::__construct($context);
        $this->_config = $config;
        $this->_storeConfig = $storeConfig;
        $this->_registry = $registry;
    }

    /**
     * Is Google AdWords active
     *
     * @return bool
     */
    public function isGoogleAdwordsActive()
    {
        return $this->_storeConfig->getConfigFlag(self::XML_PATH_ACTIVE)
            && $this->getConversionId()
            && $this->getConversionLanguage()
            && $this->getConversionFormat()
            && $this->getConversionColor()
            && $this->getConversionLabel();
    }

    /**
     * Retrieve language codes from config
     *
     * @return array
     */
    public function getLanguageCodes()
    {
        return $this->_config->getNode(self::XML_PATH_LANGUAGES)->asArray();
    }

    /**
     * Get Google AdWords conversion id
     *
     * @return string
     */
    public function getConversionId()
    {
        return $this->_storeConfig->getConfig(self::XML_PATH_CONVERSION_ID);
    }

    /**
     * Get Google AdWords conversion language
     *
     * @return string
     */
    public function getConversionLanguage()
    {
        return $this->_storeConfig->getConfig(self::XML_PATH_CONVERSION_LANGUAGE);
    }

    /**
     * Get Google AdWords conversion format
     *
     * @return int
     */
    public function getConversionFormat()
    {
        return $this->_storeConfig->getConfig(self::XML_PATH_CONVERSION_FORMAT);
    }

    /**
     * Get Google AdWords conversion color
     *
     * @return string
     */
    public function getConversionColor()
    {
        return $this->_storeConfig->getConfig(self::XML_PATH_CONVERSION_COLOR);
    }

    /**
     * Get Google AdWords conversion label
     *
     * @return string
     */
    public function getConversionLabel()
    {
        return $this->_storeConfig->getConfig(self::XML_PATH_CONVERSION_LABEL);
    }

    /**
     * Get Google AdWords conversion value type
     *
     * @return string
     */
    public function getConversionValueType()
    {
        return $this->_storeConfig->getConfig(self::XML_PATH_CONVERSION_VALUE_TYPE);
    }

    /**
     * Checks if conversion value is dynamic
     *
     * @return bool
     */
    public function isDynamicConversionValue()
    {
        return $this->getConversionValueType() == self::CONVERSION_VALUE_TYPE_DYNAMIC;
    }

    /**
     * Get Google AdWords conversion value constant
     *
     * @return string
     */
    public function getConversionValueConstant()
    {
        return $this->_storeConfig->getConfig(self::XML_PATH_CONVERSION_VALUE);
    }

    /**
     * Get Google AdWords conversion value
     *
     * @return string
     */
    public function getConversionValue()
    {
        if ($this->isDynamicConversionValue()) {
            $conversionValue = $this->_registry->registry(self::CONVERSION_VALUE_REGISTRY_NAME);
        } else {
            $conversionValue = $this->getConversionValueConstant();
        }
        return empty($conversionValue) ? self::CONVERSION_VALUE_DEFAULT : $conversionValue;
    }
}
