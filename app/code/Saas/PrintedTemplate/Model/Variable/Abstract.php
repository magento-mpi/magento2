<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Container for template variable
 *
 * Container that can restrict access to properties and method
 * with white list. Also formats return values by type
 * with format<Type>() function (if defined).
 * We have to inherit Magento_object for template filter.
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Variable_Abstract extends Magento_Object
{
    /**
     * Value of contatiner
     *
     * @var object
     */
    protected $_value;

    /**
     * White list for methods
     *
     * @var array
     */
    private $_methodList = array();

    /**
     * White list of protperties
     *
     * @var array
     */
    private $_propertyList = array();

    /**
     * Method/property suffix to check that returnable data shouldn't be formatted
     *
     * @var string
     */
    private $_rawDataSuffix = 'Raw';

    /**
     * Constructs object, check type of variable if defined
     * $_valueType and set value of variable
     *
     * @param object $value
     */
    public function __construct($value = null)
    {
        $this->_value = $value;
        $this->_initVariable();
    }

    /**
     * Set additional data to variable object
     *
     * @return Saas_PrintedTemplate_Model_Variable_Abstract
     */
    protected function _initVariable()
    {
        return $this;
    }

    /**
     * Converts one_two_theee to OneTwoThree
     *
     * @param string $value
     * @return $value
     */
    protected function _camelize($value)
    {
        return uc_words($value, '');
    }

    /**
     * Returns config model singleton
     *
     * @return Saas_PrintedTemplate_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('Saas_PrintedTemplate_Model_Config');
    }

    /**
     * Set list by config for variable
     *
     * Loads config from variables/$variableName/fields
     * and set name of properties and approperiate type from
     * this config array. If no type sets text
     *
     * @param string $variableName Key from variables/ of the module confg section
     * @return Saas_PrintedTemplate_Model_Template_Variable_Abstract Self
     * @see Saas_PrintedTemplate_Model_Config
     */
    protected function _setListsFromConfig($variableName)
    {
        $fields = $this->_getConfig()->getConfigSectionArray("variables/$variableName/fields");
        foreach ($fields as $name => $config) {
            $type = isset($config['type']) ? $config['type'] : 'text';
            $this->_addPropertyToList($name, $type);
            $this->_addMethodToList('get' . $this->_camelize($name), $type);
        }

        return $this;
    }

    /**
     * Add property to list (allowed or not allowed items)
     *
     * @param string $name
     * @param string $type
     * @return Saas_PrintedTemplate_Model_Template_Variable_Abstract Self
     */
    protected function _addPropertyToList($name, $type)
    {
        $this->_propertyList[$name] = array('type' => $type);

        return $this;
    }

    /**
     * Add method to list (allowed or not allowed items)
     *
     * @param string $name
     * @param string $type
     * @return Saas_PrintedTemplate_Model_Template_Variable_Abstract Self
     */
    protected function _addMethodToList($name, $type)
    {
        $this->_methodList[$name] = array('type' => $type);

        return $this;
    }

    /**
     * Removes property from list
     *
     * @param string $name
     * @return Saas_PrintedTemplate_Model_Template_Variable_Abstract Self
     */
    protected function _removePropertyFromList($name)
    {
        unset($this->_propertyList[$name]);

        return $this;
    }

    /**
     * Removes method from list
     *
     * @param string $name
     * @return Saas_PrintedTemplate_Model_Template_Variable_Abstract Self
     */
    protected function _removeMethodFromList($name)
    {
        unset($this->_methodList[$name]);

        return $this;
    }

    /**
     * Checks if function format<Type>Exists() format value with it
     *
     * @param mixed $value
     * @param string $type
     * @return string
     */
    protected function _format($value, $type)
    {
        $formater = 'format' . $this->_camelize($type);

        return method_exists($this, $formater)
            ? $this->$formater($value)
            : $value;
    }

    /**
     * Check if name has raw data suffix
     *
     * @param string $name
     * @return bool
     */
    protected function _hasRawDataSuffix($name)
    {
        return !strcasecmp(substr($name, -strlen($this->_rawDataSuffix)), $this->_rawDataSuffix);
    }

    /**
     * Strips raw data suffix from name
     *
     * @param string $name
     * @return string
     */
    protected function _stripRawDataSuffix($name)
    {
        return substr($name, 0, -strlen($this->_rawDataSuffix) - 1);
    }

    /**
     * Check is method allowed and call it.
     *
     * Method is allowed if mode iswhite list and method IS in the list
     * of if mode is blacklist and method IS NOT in the list.
     *
     * @param string $name
     * @param array $arguments
     * @throws BadMethodCallException If method is not allowed or not public
     */
    public function __call($name, $arguments)
    {
        if ($raw = $this->_hasRawDataSuffix($name)) {
            $name = $this->_stripRawDataSuffix($name);
        }
        if (!isset($this->_methodList[$name]) || !is_callable(array($this->_value, $name))) {
            return '{{Incorrect call}}';
        }
        $type = $this->_methodList[$name]['type'];
        if ($raw) {
            $type .= $this->_rawDataSuffix;
        }

        return $this->_format(
            call_user_func_array(array($this->_value, $name), $arguments),
            $type
        );
    }

    /**
     * If property is allowed returns its formated value
     *
     * Use _format() to format value to human readable form.
     *
     * @param mixed $name
     * @return mixed
     */
    protected function _getProperty($name)
    {
        if ($raw = $this->_hasRawDataSuffix($name)) {
            $name = $this->_stripRawDataSuffix($name);
        }
        if (!isset($this->_propertyList[$name])) {
            return '{{Incorrect property}}';
        }
        $type = $this->_propertyList[$name]['type'];
        if ($raw) {
            $type .= $this->_rawDataSuffix;
        }

        $getterName = 'get' . $this->_camelize($name);

        return (!$raw && method_exists($this, $getterName))
            ? $this->$getterName()
            : $this->_format($this->_value->$getterName(), $type);
    }

    /**
     * Wrap getData call
     *
     * @see Magento_Object::getData()
     * @param string $name Name
     * @param string $index Does not take into account
     * @return mixed
     */
    public function getData($name = '', $index = null)
    {
        return $this->_getProperty($name);
    }

    /**
     * Returns Data helper
     *
     * @return  Saas_PrintedTemplate_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('Saas_PrintedTemplate_Helper_Data');
    }

    /**
     * Returns current locale
     *
     * @return Zend_Locale
     */
    protected function _getLocale()
    {
        return Mage::app()->getLocale()->getLocale();
    }

    /**
     * Check that some float number is equal to zero with specified precision
     *
     * @param string|float|int $number
     * @param int $precision
     * @return boolean
     */
    protected function _isFloatEqualToZero($number, $precision = 5)
    {
        return abs($number) < pow(10, -$precision);
    }

    /*****************
     *   Formaters   *
     *****************/

    /**
     * Returns $value param without modifications
     *
     * @param string $value
     * @return string
     */
    public function formatText($value)
    {
        return $value;
    }

    /**
     * Formats date with core/data helper
     *
     * @param string|Zend_Date $value
     * @return string
     */
    public function formatDate($value)
    {
        return $value ? $this->_getCoreHelper()->formatDate($value) : '';
    }

    /**
     * Formats currency with core/data helper
     *
     * @param string $value
     * @return string
     */
    public function formatCurrency($value)
    {
        return ($value !== null) ? $this->_getCoreHelper()->formatCurrency($value) : '';
    }

    /**
     * Formats absolute value of currency with core/data helper
     *
     * @param string $value
     * @return string
     */
    public function formatCurrencyAbs($value)
    {
        return ($value !== null) ? $this->_getCoreHelper()->formatCurrency(abs($value)) : '';
    }

    /**
     * Formats currency value as float
     *
     * @param string $value
     * @return float
     */
    public function formatCurrencyAbsRaw($value)
    {
        return (null !== $value) ? abs((float)$value) : '';
    }

    /**
     * Formats currency value as float
     *
     * @param string $value
     * @return float
     */
    public function formatCurrencyRaw($value)
    {
        return (null !== $value) ? (float)$value : '';
    }

    /**
     * Formats boolean value to string Yes/No
     *
     * @param bool $value
     * @return string
     */
    public function formatYesNo($value)
    {
        return __($value ? 'Yes' : 'No');
    }

    /**
     * Formats string value as boolean
     *
     * @param string $value
     * @return boolean
     */
    public function formatYesNoRaw($value)
    {
        return (bool) $value;
    }

    /**
     * Rounds number by 4 position after dot
     * and render taking into account locale
     *
     * @param number $value
     * @return string
     */
    public function formatDecimal($value)
    {
        return Zend_Locale_Format::toNumber((float)$value, array('locale' => $this->_getLocale()));
    }

    /**
     * Format percentage value to display
     *
     * @param number $value
     * @return string
     */
    public function formatPercent($value)
    {
        return ($value === null) ? '' : $this->formatDecimal(round($value, 2)) . '%';
    }

    /**
     * Formats compound ID of taxes
     *
     * @param Saas_PrintedTemplate_Model_Tax_CompoundId $value
     * @return string
     */
    public function formatCompoundId(Saas_PrintedTemplate_Model_Tax_CompoundId $value)
    {
        return join(
            __(' then '),
            array_map(array($this, '_formatAfterPart'), $value->toArray())
        );
    }

    /**
     * Formater for after part of compound tax ID
     *
     * @param mixed $value
     * @return string
     */
    protected function _formatAfterPart($value)
    {
        return is_array($value)
            ? join(__(' and '), array_map(array($this, 'formatPercent'), $value))
            : $this->formatPercent($value);
    }

    /**
     * @return Mage_Core_Helper_Data
     */
    protected function _getCoreHelper()
    {
        return Mage::helper('Mage_Core_Helper_Data');
    }
}


