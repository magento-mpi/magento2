<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Backend;

class Baseurl extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\Core\Model\Page\Asset\MergeService
     */
    protected $_mergeService;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param \Magento\Core\Model\Page\Asset\MergeService $mergeService
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        \Magento\Core\Model\Page\Asset\MergeService $mergeService,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_mergeService = $mergeService;
        parent::__construct(
            $context,
            $registry,
            $storeManager,
            $config,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Validate a base URL field value
     *
     * @return \Magento\Backend\Model\Config\Backend\Baseurl
     * @throws \Magento\Core\Exception
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        try {
            if (!$this->_validateUnsecure($value) && !$this->_validateSecure($value)) {
                $this->_validateFullyQualifiedUrl($value);
            }
        } catch (\Magento\Core\Exception $e) {
            $field = $this->getFieldConfig();
            $label = ($field && is_array($field) ? $field['label'] : 'value');
            $msg = __('Invalid %1. %2', $label, $e->getMessage());
            $error = new \Magento\Core\Exception($msg, 0, $e);
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
            case \Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_URL:
                $this->_assertValuesOrUrl(array('{{base_url}}'), $value);
                break;
            case \Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_LINK_URL:
                $this->_assertStartsWithValuesOrUrl($placeholders, $value);
                break;
            case \Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_STATIC_URL:
            case \Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_CACHE_URL:
            case \Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_LIB_URL:
            case \Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_MEDIA_URL:
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
            case \Magento\Core\Model\Store::XML_PATH_SECURE_BASE_URL:
                $this->_assertValuesOrUrl(array('{{base_url}}', '{{unsecure_base_url}}'), $value);
                break;
            case \Magento\Core\Model\Store::XML_PATH_SECURE_BASE_LINK_URL:
                $this->_assertStartsWithValuesOrUrl($placeholders, $value);
                break;
            case \Magento\Core\Model\Store::XML_PATH_SECURE_BASE_STATIC_URL:
            case \Magento\Core\Model\Store::XML_PATH_SECURE_BASE_CACHE_URL:
            case \Magento\Core\Model\Store::XML_PATH_SECURE_BASE_LIB_URL:
            case \Magento\Core\Model\Store::XML_PATH_SECURE_BASE_MEDIA_URL:
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
     * @throws \Magento\Core\Exception
     */
    private function _assertValuesOrUrl(array $values, $value)
    {
        if (!in_array($value, $values) && !$this->_isFullyQualifiedUrl($value)) {
            throw new \Magento\Core\Exception(__('Value must be a URL or one of placeholders: %1',
                implode(',', $values)));
        }
    }

    /**
     * Value starts with one of provided items or is a URL
     *
     * @param array $values
     * @param string $value
     * @throws \Magento\Core\Exception
     */
    private function _assertStartsWithValuesOrUrl(array $values, $value)
    {
        $quoted = array_map('preg_quote', $values, array_fill(0, count($values), '/'));
        if (!preg_match('/^(' . implode('|', $quoted) . ')(.+\/)?$/', $value) && !$this->_isFullyQualifiedUrl($value)) {
            throw new \Magento\Core\Exception(
                __('Specify a URL or path that starts with placeholder(s): %1.', implode(', ', $values)));
        }
    }

    /**
     * Value starts with, empty or is a URL
     *
     * @param array $values
     * @param string $value
     * @throws \Magento\Core\Exception
     */
    private function _assertStartsWithValuesOrUrlOrEmpty(array $values, $value)
    {
        if (empty($value)) {
            return;
        }
        try {
            $this->_assertStartsWithValuesOrUrl($values, $value);
        } catch (\Magento\Core\Exception $e) {
            $msg = __('%1 An empty value is allowed as well.', $e->getMessage());
            $error = new \Magento\Core\Exception($msg, 0, $e);
            throw $error;
        }
    }

    /**
     * Default validation of a URL
     *
     * @param string $value
     * @throws \Magento\Core\Exception
     */
    private function _validateFullyQualifiedUrl($value)
    {
        if (!$this->_isFullyQualifiedUrl($value)) {
            throw new \Magento\Core\Exception(
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
                case \Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_URL:
                case \Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_MEDIA_URL:
                case \Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_LIB_URL:
                case \Magento\Core\Model\Store::XML_PATH_SECURE_BASE_URL:
                case \Magento\Core\Model\Store::XML_PATH_SECURE_BASE_MEDIA_URL:
                case \Magento\Core\Model\Store::XML_PATH_SECURE_BASE_LIB_URL:
                    $this->_mergeService->cleanMergedJsCss();
                    break;
            }
        }
    }
}
