<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Config_Backend_Baseurl extends Magento_Core_Model_Config_Value
{
    /**
     * @var Magento_Core_Model_Page_Asset_MergeService
     */
    protected $_mergeService;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Core_Model_Page_Asset_MergeService $mergeService
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Config $coreConfig,
        Magento_Core_Model_Page_Asset_MergeService $mergeService,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_mergeService = $mergeService;
        parent::__construct(
            $context,
            $coreConfig,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Validate a base URL field value
     *
     * @return Magento_Backend_Model_Config_Backend_Baseurl
     * @throws Magento_Core_Exception
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        try {
            if (!$this->_validateUnsecure($value) && !$this->_validateSecure($value)) {
                $this->_validateFullyQualifiedUrl($value);
            }
        } catch (Magento_Core_Exception $e) {
            $field = $this->getFieldConfig();
            $label = ($field && is_array($field) ? $field['label'] : 'value');
            $msg = __('Invalid %1. %2', $label, $e->getMessage());
            $error = new Magento_Core_Exception($msg, 0, $e);
            throw $error;
        }
    }

    /**
     * Validation sub-routine for unsecure base URLs
     *
     * @param string $value
     * @return bool
     */
    private function _validateUnsecure($value)
    {
        $placeholders = array('{{unsecure_base_url}}');
        switch ($this->getPath()) {
            case Magento_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL:
                $this->_assertValuesOrUrl(array('{{base_url}}'), $value);
                break;
            case Magento_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL:
                $this->_assertStartsWithValuesOrUrl($placeholders, $value);
                break;
            case Magento_Core_Model_Store::XML_PATH_UNSECURE_BASE_STATIC_URL:
            case Magento_Core_Model_Store::XML_PATH_UNSECURE_BASE_CACHE_URL:
            case Magento_Core_Model_Store::XML_PATH_UNSECURE_BASE_LIB_URL:
            case Magento_Core_Model_Store::XML_PATH_UNSECURE_BASE_MEDIA_URL:
                $this->_assertStartsWithValuesOrUrlOrEmpty($placeholders, $value);
                break;
            default:
                return false;
        }
        return true;
    }

    /**
     * Validation sub-routine for secure base URLs
     *
     * @param string $value
     * @return bool
     */
    private function _validateSecure($value)
    {
        $placeholders = array('{{unsecure_base_url}}', '{{secure_base_url}}');
        switch ($this->getPath()) {
            case Magento_Core_Model_Store::XML_PATH_SECURE_BASE_URL:
                $this->_assertValuesOrUrl(array('{{base_url}}', '{{unsecure_base_url}}'), $value);
                break;
            case Magento_Core_Model_Store::XML_PATH_SECURE_BASE_LINK_URL:
                $this->_assertStartsWithValuesOrUrl($placeholders, $value);
                break;
            case Magento_Core_Model_Store::XML_PATH_SECURE_BASE_STATIC_URL:
            case Magento_Core_Model_Store::XML_PATH_SECURE_BASE_CACHE_URL:
            case Magento_Core_Model_Store::XML_PATH_SECURE_BASE_LIB_URL:
            case Magento_Core_Model_Store::XML_PATH_SECURE_BASE_MEDIA_URL:
                $this->_assertStartsWithValuesOrUrlOrEmpty($placeholders, $value);
                break;
            default:
                return false;
        }
        return true;
    }

    /**
     * Value equals to one of provided items or is a URL
     *
     * @param array $values
     * @param string $value
     * @throws Magento_Core_Exception
     */
    private function _assertValuesOrUrl(array $values, $value)
    {
        if (!in_array($value, $values) && !$this->_isFullyQualifiedUrl($value)) {
            throw new Magento_Core_Exception(__('Value must be a URL or one of placeholders: %1',
                implode(',', $values)));
        }
    }

    /**
     * Value starts with one of provided items or is a URL
     *
     * @param array $values
     * @param string $value
     * @throws Magento_Core_Exception
     */
    private function _assertStartsWithValuesOrUrl(array $values, $value)
    {
        $quoted = array_map('preg_quote', $values, array_fill(0, count($values), '/'));
        if (!preg_match('/^(' . implode('|', $quoted) . ')(.+\/)?$/', $value) && !$this->_isFullyQualifiedUrl($value)) {
            throw new Magento_Core_Exception(
                __('Specify a URL or path that starts with placeholder(s): %1.', implode(', ', $values)));
        }
    }

    /**
     * Value starts with, empty or is a URL
     *
     * @param array $values
     * @param string $value
     * @throws Magento_Core_Exception
     */
    private function _assertStartsWithValuesOrUrlOrEmpty(array $values, $value)
    {
        if (empty($value)) {
            return;
        }
        try {
            $this->_assertStartsWithValuesOrUrl($values, $value);
        } catch (Magento_Core_Exception $e) {
            $msg = __('%1 An empty value is allowed as well.', $e->getMessage());
            $error = new Magento_Core_Exception($msg, 0, $e);
            throw $error;
        }
    }

    /**
     * Default validation of a URL
     *
     * @param string $value
     * @throws Magento_Core_Exception
     */
    private function _validateFullyQualifiedUrl($value)
    {
        if (!$this->_isFullyQualifiedUrl($value)) {
            throw new Magento_Core_Exception(
                __('Specify a fully qualified URL.')
            );
        }
    }

    /**
     * Whether the provided value can be considered as a fully qualified URL
     *
     * @param string $value
     * @return bool
     */
    private function _isFullyQualifiedUrl($value)
    {
        $url = parse_url($value);
        return isset($url['scheme']) && isset($url['host']) && preg_match('/\/$/', $value);
    }

    /**
     * Clean compiled JS/CSS when updating url configuration settings
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            switch ($this->getPath()) {
                case Magento_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL:
                case Magento_Core_Model_Store::XML_PATH_UNSECURE_BASE_MEDIA_URL:
                case Magento_Core_Model_Store::XML_PATH_UNSECURE_BASE_LIB_URL:
                case Magento_Core_Model_Store::XML_PATH_SECURE_BASE_URL:
                case Magento_Core_Model_Store::XML_PATH_SECURE_BASE_MEDIA_URL:
                case Magento_Core_Model_Store::XML_PATH_SECURE_BASE_LIB_URL:
                    $this->_mergeService->cleanMergedJsCss();
                    break;
            }
        }
    }
}
