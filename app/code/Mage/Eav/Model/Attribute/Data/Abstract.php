<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV Attribute Abstract Data Model
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Eav_Model_Attribute_Data_Abstract
{
    /**
     * Attribute instance
     *
     * @var Mage_Eav_Model_Attribute
     */
    protected $_attribite;

    /**
     * Entity instance
     *
     * @var Mage_Core_Model_Abstract
     */
    protected $_entity;

    /**
     * Request Scope name
     *
     * @var string
     */
    protected $_requestScope;

    /**
     * Scope visibility flag
     *
     * @var boolean
     */
    protected $_requestScopeOnly    = true;

    /**
     * Is AJAX request flag
     *
     * @var boolean
     */
    protected $_isAjax              = false;

    /**
     * Array of full extracted data
     * Needed for depends attributes
     *
     * @var array
     */
    protected $_extractedData       = array();

    /**
     * Mage_Core_Model_LocaleInterface FORMAT
     *
     * @var string
     */
    protected $_dateFilterFormat;

    /**
     * Set attribute instance
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return Mage_Eav_Model_Attribute_Data_Abstract
     */
    public function setAttribute(Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        $this->_attribite = $attribute;
        return $this;
    }

    /**
     * Return Attribute instance
     *
     * @throws Mage_Core_Exception
     * @return Mage_Eav_Model_Attribute
     */
    public function getAttribute()
    {
        if (!$this->_attribite) {
            Mage::throwException(__('Attribute object is undefined'));
        }
        return $this->_attribite;
    }

    /**
     * Set Request scope
     *
     * @param string $scope
     * @return string
     */
    public function setRequestScope($scope)
    {
        $this->_requestScope = $scope;
        return $this;
    }

    /**
     * Set scope visibility
     * Search value only in scope or search value in scope and global
     *
     * @param boolean $flag
     * @return Mage_Eav_Model_Attribute_Data_Abstract
     */
    public function setRequestScopeOnly($flag)
    {
        $this->_requestScopeOnly = (bool)$flag;
        return $this;
    }

    /**
     * Set entity instance
     *
     * @param Mage_Core_Model_Abstract $entity
     * @return Mage_Eav_Model_Attribute_Data_Abstract
     */
    public function setEntity(Mage_Core_Model_Abstract $entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    /**
     * Returns entity instance
     *
     * @return Mage_Core_Model_Abstract
     */
    public function getEntity()
    {
        if (!$this->_entity) {
            Mage::throwException(__('Entity object is undefined'));
        }
        return $this->_entity;
    }

    /**
     * Set array of full extracted data
     *
     * @param array $data
     * @return Mage_Eav_Model_Attribute_Data_Abstract
     */
    public function setExtractedData(array $data)
    {
        $this->_extractedData = $data;
        return $this;
    }

    /**
     * Return extracted data
     *
     * @param string $index
     * @return mixed
     */
    public function getExtractedData($index = null)
    {
        if (!is_null($index)) {
            if (isset($this->_extractedData[$index])) {
                return $this->_extractedData[$index];
            }
            return null;
        }
        return $this->_extractedData;
    }

    /**
     * Apply attribute input filter to value
     *
     * @param string $value
     * @return string
     */
    protected function _applyInputFilter($value)
    {
        if ($value === false) {
            return false;
        }

        $filter = $this->_getFormFilter();
        if ($filter) {
            $value = $filter->inputFilter($value);
        }

        return $value;
    }

    /**
     * Return Data Form Input/Output Filter
     *
     * @return Magento_Data_Form_Filter_Interface|false
     */
    protected function _getFormFilter()
    {
        $filterCode = $this->getAttribute()->getInputFilter();
        if ($filterCode) {
            $filterClass = 'Magento_Data_Form_Filter_' . ucfirst($filterCode);
            if ($filterCode == 'date') {
                $filter = new $filterClass($this->_dateFilterFormat(), Mage::app()->getLocale()->getLocale());
            } else {
                $filter = new $filterClass();
            }
            return $filter;
        }
        return false;
    }

    /**
     * Get/Set/Reset date filter format
     *
     * @param string|null|false $format
     * @return Mage_Eav_Model_Attribute_Data_Abstract|string
     */
    protected function _dateFilterFormat($format = null)
    {
        if (is_null($format)) {
            // get format
            if (is_null($this->_dateFilterFormat)) {
                $this->_dateFilterFormat = Mage_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT;
            }
            return Mage::app()->getLocale()->getDateFormat($this->_dateFilterFormat);
        } else if ($format === false) {
            // reset value
            $this->_dateFilterFormat = null;
            return $this;
        }

        $this->_dateFilterFormat = $format;
        return $this;
    }

    /**
     * Apply attribute output filter to value
     *
     * @param string $value
     * @return string
     */
    protected function _applyOutputFilter($value)
    {
        $filter = $this->_getFormFilter();
        if ($filter) {
            $value = $filter->outputFilter($value);
        }

        return $value;
    }

    /**
     * Validate value by attribute input validation rule
     *
     * @param string $value
     * @return string
     */
    protected function _validateInputRule($value)
    {
        // skip validate empty value
        if (empty($value)) {
            return true;
        }

        $label         = $this->getAttribute()->getStoreLabel();
        $validateRules = $this->getAttribute()->getValidateRules();

        if (!empty($validateRules['input_validation'])) {
            switch ($validateRules['input_validation']) {
                case 'alphanumeric':
                    $validator = new Zend_Validate_Alnum(true);
                    $validator->setMessage(
                        __('"%1" invalid type entered.', $label),
                        Zend_Validate_Alnum::INVALID
                    );
                    $validator->setMessage(
                        __('"%1" contains non-alphabetic or non-numeric characters.', $label),
                        Zend_Validate_Alnum::NOT_ALNUM
                    );
                    $validator->setMessage(
                        __('"%1" is an empty string.', $label),
                        Zend_Validate_Alnum::STRING_EMPTY
                    );
                    if (!$validator->isValid($value)) {
                        return $validator->getMessages();
                    }
                    break;
                case 'numeric':
                    $validator = new Zend_Validate_Digits();
                    $validator->setMessage(
                        __('"%1" invalid type entered.', $label),
                        Zend_Validate_Digits::INVALID
                    );
                    $validator->setMessage(
                        __('"%1" contains non-numeric characters.', $label),
                        Zend_Validate_Digits::NOT_DIGITS
                    );
                    $validator->setMessage(
                        __('"%1" is an empty string.', $label),
                        Zend_Validate_Digits::STRING_EMPTY
                    );
                    if (!$validator->isValid($value)) {
                        return $validator->getMessages();
                    }
                    break;
                case 'alpha':
                    $validator = new Zend_Validate_Alpha(true);
                    $validator->setMessage(
                        __('"%1" invalid type entered.', $label),
                        Zend_Validate_Alpha::INVALID
                    );
                    $validator->setMessage(
                        __('"%1" contains non-alphabetic characters.', $label),
                        Zend_Validate_Alpha::NOT_ALPHA
                    );
                    $validator->setMessage(
                        __('"%1" is an empty string.', $label),
                        Zend_Validate_Alpha::STRING_EMPTY
                    );
                    if (!$validator->isValid($value)) {
                        return $validator->getMessages();
                    }
                    break;
                case 'email':
        /**
        __("'%value%' appears to be a DNS hostname but the given punycode notation cannot be decoded")
        __("Invalid type given. String expected")
        __("'%value%' appears to be a DNS hostname but contains a dash in an invalid position")
        __("'%value%' does not match the expected structure for a DNS hostname")
        __("'%value%' appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'")
        __("'%value%' does not appear to be a valid local network name")
        __("'%value%' does not appear to be a valid URI hostname")
        __("'%value%' appears to be an IP address, but IP addresses are not allowed")
        __("'%value%' appears to be a local network name but local network names are not allowed")
        __("'%value%' appears to be a DNS hostname but cannot extract TLD part")
        __("'%value%' appears to be a DNS hostname but cannot match TLD against known list")
        */
                    $validator = new Zend_Validate_EmailAddress();
                    $validator->setMessage(
                        __('"%1" invalid type entered.', $label),
                        Zend_Validate_EmailAddress::INVALID
                    );
                    $validator->setMessage(
                        __('"%1" is not a valid email address.', $label),
                        Zend_Validate_EmailAddress::INVALID_FORMAT
                    );
                    $validator->setMessage(
                        __('"%1" is not a valid hostname.', $label),
                        Zend_Validate_EmailAddress::INVALID_HOSTNAME
                    );
                    $validator->setMessage(
                        __('"%1" is not a valid hostname.', $label),
                        Zend_Validate_EmailAddress::INVALID_MX_RECORD
                    );
                    $validator->setMessage(
                        __('"%1" is not a valid hostname.', $label),
                        Zend_Validate_EmailAddress::INVALID_MX_RECORD
                    );
                    $validator->setMessage(
                        __('"%1" is not a valid email address.', $label),
                        Zend_Validate_EmailAddress::DOT_ATOM
                    );
                    $validator->setMessage(
                        __('"%1" is not a valid email address.', $label),
                        Zend_Validate_EmailAddress::QUOTED_STRING
                    );
                    $validator->setMessage(
                        __('"%1" is not a valid email address.', $label),
                        Zend_Validate_EmailAddress::INVALID_LOCAL_PART
                    );
                    $validator->setMessage(
                        __('"%1" exceeds the allowed length.', $label),
                        Zend_Validate_EmailAddress::LENGTH_EXCEEDED
                    );
                    $validator->setMessage(
                        __("'%value%' appears to be an IP address, but IP addresses are not allowed."),
                        Zend_Validate_Hostname::IP_ADDRESS_NOT_ALLOWED
                    );
                    $validator->setMessage(
                        __("'%value%' appears to be a DNS hostname but cannot match TLD against known list."),
                        Zend_Validate_Hostname::UNKNOWN_TLD
                    );
                    $validator->setMessage(
                        __("'%value%' appears to be a DNS hostname but contains a dash in an invalid position."),
                        Zend_Validate_Hostname::INVALID_DASH
                    );
                    $validator->setMessage(
                        __("'%value%' appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'."),
                        Zend_Validate_Hostname::INVALID_HOSTNAME_SCHEMA
                    );
                    $validator->setMessage(
                        __("'%value%' appears to be a DNS hostname but cannot extract TLD part."),
                        Zend_Validate_Hostname::UNDECIPHERABLE_TLD
                    );
                    $validator->setMessage(
                        __("'%value%' does not appear to be a valid local network name."),
                        Zend_Validate_Hostname::INVALID_LOCAL_NAME
                    );
                    $validator->setMessage(
                        __("'%value%' appears to be a local network name but local network names are not allowed."),
                        Zend_Validate_Hostname::LOCAL_NAME_NOT_ALLOWED
                    );
                    $validator->setMessage(
                        __("'%value%' appears to be a DNS hostname but the given punycode notation cannot be decoded."),
                        Zend_Validate_Hostname::CANNOT_DECODE_PUNYCODE
                    );
                    if (!$validator->isValid($value)) {
                        return array_unique($validator->getMessages());
                    }
                    break;
                case 'url':
                    $parsedUrl = parse_url($value);
                    if ($parsedUrl === false || empty($parsedUrl['scheme']) || empty($parsedUrl['host'])) {
                        return array(__('"%1" is not a valid URL.', $label));
                    }
                    $validator = new Zend_Validate_Hostname();
                    if (!$validator->isValid($parsedUrl['host'])) {
                        return array(__('"%1" is not a valid URL.', $label));
                    }
                    break;
                case 'date':
                    $validator = new Zend_Validate_Date(Magento_Date::DATE_INTERNAL_FORMAT);
                    $validator->setMessage(
                        __('"%1" invalid type entered.', $label),
                        Zend_Validate_Date::INVALID
                    );
                    $validator->setMessage(
                        __('"%1" is not a valid date.', $label),
                        Zend_Validate_Date::INVALID_DATE
                    );
                    $validator->setMessage(
                        __('"%1" does not fit the entered date format.', $label),
                        Zend_Validate_Date::FALSEFORMAT
                    );
                    if (!$validator->isValid($value)) {
                       return array_unique($validator->getMessages());
                    }

                    break;
            }
        }
        return true;
    }

    /**
     * Set is AJAX Request flag
     *
     * @param boolean $flag
     * @return Mage_Eav_Model_Attribute_Data_Abstract
     */
    public function setIsAjaxRequest($flag = true)
    {
        $this->_isAjax = (bool)$flag;
        return $this;
    }

    /**
     * Return is AJAX Request
     *
     * @return boolean
     */
    public function getIsAjaxRequest()
    {
        return $this->_isAjax;
    }

    /**
     * Return Original Attribute value from Request
     *
     * @param Zend_Controller_Request_Http $request
     * @return mixed
     */
    protected function _getRequestValue(Zend_Controller_Request_Http $request)
    {
        $attrCode  = $this->getAttribute()->getAttributeCode();
        if ($this->_requestScope) {
            if (strpos($this->_requestScope, '/') !== false) {
                $params = $request->getParams();
                $parts = explode('/', $this->_requestScope);
                foreach ($parts as $part) {
                    if (isset($params[$part])) {
                        $params = $params[$part];
                    } else {
                        $params = array();
                    }
                }
            } else {
                $params = $request->getParam($this->_requestScope);
            }

            if (isset($params[$attrCode])) {
                $value = $params[$attrCode];
            } else {
                $value = false;
            }

            if (!$this->_requestScopeOnly && $value === false) {
                $value = $request->getParam($attrCode, false);
            }
        } else {
            $value = $request->getParam($attrCode, false);
        }
        return $value;
    }

    /**
     * Extract data from request and return value
     *
     * @param Zend_Controller_Request_Http $request
     * @return array|string
     */
    abstract public function extractValue(Zend_Controller_Request_Http $request);

    /**
     * Validate data
     *
     * @param array|string $value
     * @throws Mage_Core_Exception
     * @return boolean
     */
    abstract public function validateValue($value);

    /**
     * Export attribute value to entity model
     *
     * @param array|string $value
     * @return Mage_Eav_Model_Attribute_Data_Abstract
     */
    abstract public function compactValue($value);

    /**
     * Restore attribute value from SESSION to entity model
     *
     * @param array|string $value
     * @return Mage_Eav_Model_Attribute_Data_Abstract
     */
    abstract public function restoreValue($value);

    /**
     * Return formated attribute value from entity model
     *
     * @param string $format
     * @return string|array
     */
    abstract public function outputValue($format = Mage_Eav_Model_Attribute_Data::OUTPUT_FORMAT_TEXT);
}
