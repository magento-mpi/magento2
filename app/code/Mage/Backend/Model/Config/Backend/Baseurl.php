<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Config_Backend_Baseurl extends Mage_Core_Model_Config_Data
{
    /**
     * Validate a base URL field value
     *
     * @return Mage_Backend_Model_Config_Backend_Baseurl
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        try {
            if (!$this->_validateUnsecure($value) && !$this->_validateSecure($value)) {
                $this->_validateFullyQualifiedUrl($value);
            }
        } catch (Mage_Core_Exception $e) {
            $field = $this->getFieldConfig();
            $label = ($field && is_array($field) ? $field['label'] : 'value');
            $msg = Mage::helper('Mage_Backend_Helper_Data')->__('Invalid %s. %s', $label, $e->getMessage());
            $error = new Mage_Core_Exception($msg, 0, $e);
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
            case Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL:
                $this->_assertValuesOrUrl(array('{{base_url}}'), $value);
                break;
            case Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL:
                $this->_assertStartsWithValuesOrUrl($placeholders, $value);
                break;
            case Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_STATIC_URL:
            case Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LIB_URL:
            case Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_MEDIA_URL:
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
            case Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL:
                $this->_assertValuesOrUrl(array('{{base_url}}', '{{unsecure_base_url}}'), $value);
                break;
            case Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LINK_URL:
                $this->_assertStartsWithValuesOrUrl($placeholders, $value);
                break;
            case Mage_Core_Model_Store::XML_PATH_SECURE_BASE_STATIC_URL:
            case Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LIB_URL:
            case Mage_Core_Model_Store::XML_PATH_SECURE_BASE_MEDIA_URL:
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
     * @throws Mage_Core_Exception
     */
    private function _assertValuesOrUrl(array $values, $value)
    {
        if (!in_array($value, $values) && !$this->_isFullyQualifiedUrl($value)) {
            throw new Mage_Core_Exception(Mage::helper('Mage_Backend_Helper_Data')
                ->__('Value must be a URL or one of placeholders: %s', implode(',', $values)));
        }
    }

    /**
     * Value starts with one of provided items or is a URL
     *
     * @param array $values
     * @param string $value
     * @throws Mage_Core_Exception
     */
    private function _assertStartsWithValuesOrUrl(array $values, $value)
    {
        $quoted = array_map('preg_quote', $values, array_fill(0, count($values), '/'));
        if (!preg_match('/^(' . implode('|', $quoted) . ')(.+\/)?$/', $value) && !$this->_isFullyQualifiedUrl($value)) {
            throw new Mage_Core_Exception(Mage::helper('Mage_Backend_Helper_Data')
                ->__('Specify a URL or path that starts with placeholder(s): %s.', implode(', ', $values)));
        }
    }

    /**
     * Value starts with, empty or is a URL
     *
     * @param array $values
     * @param string $value
     * @throws Mage_Core_Exception
     */
    private function _assertStartsWithValuesOrUrlOrEmpty(array $values, $value)
    {
        if (empty($value)) {
            return;
        }
        try {
            $this->_assertStartsWithValuesOrUrl($values, $value);
        } catch (Mage_Core_Exception $e) {
            $msg = Mage::helper('Mage_Backend_Helper_Data')
                ->__('%s An empty value is allowed as well.', $e->getMessage());
            $error = new Mage_Core_Exception($msg, 0, $e);
            throw $error;
        }
    }

    /**
     * Default validation of a URL
     *
     * @param string $value
     * @throws Mage_Core_Exception
     */
    private function _validateFullyQualifiedUrl($value)
    {
        if (!$this->_isFullyQualifiedUrl($value)) {
            throw new Mage_Core_Exception(
                Mage::helper('Mage_Backend_Helper_Data')->__('Specify a fully qualified URL.')
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
                case Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL:
                case Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_MEDIA_URL:
                case Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LIB_URL:
                case Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL:
                case Mage_Core_Model_Store::XML_PATH_SECURE_BASE_MEDIA_URL:
                case Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LIB_URL:
                    Mage::getDesign()->cleanMergedJsCss();
                    break;
            }
        }
    }
}