<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Design_Backend_Exceptions extends Magento_Backend_Model_Config_Backend_Serialized_Array
{
    /**
     * Design package instance
     *
     * @var Magento_Core_Model_View_DesignInterface
     */
    protected $_design = null;

    /**
     * @param Magento_Core_Model_View_DesignInterface $design
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_View_DesignInterface $design,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Resource_Abstract $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_design = $design;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Validate value
     *
     * @throws Magento_Core_Exception if there is no field value, search value is empty or regular expression is not valid
     */
    protected function _beforeSave()
    {
        $design = clone $this->_design; // For value validations
        $exceptions = $this->getValue();
        foreach ($exceptions as $rowKey => $row) {
            if ($rowKey === '__empty') {
                continue;
            }

            // Validate that all values have come
            foreach (array('search', 'value') as $fieldName) {
                if (!isset($row[$fieldName])) {
                    Mage::throwException(
                        __("Exception does not contain field '{$fieldName}'")
                    );
                }
            }

            // Empty string (match all) is not supported, because it means setting a default theme. Remove such entries.
            if (!strlen($row['search'])) {
                unset($exceptions[$rowKey]);
                continue;
            }

            // Validate the theme value
            $design->setDesignTheme($row['value'], Magento_Core_Model_App_Area::AREA_FRONTEND);

            // Compose regular exception pattern
            $exceptions[$rowKey]['regexp'] = $this->_composeRegexp($row['search']);
        }
        $this->setValue($exceptions);

        return parent::_beforeSave();
    }

    /**
     * Composes regexp by user entered value
     *
     * @param string $search
     * @return string
     * @throws Magento_Core_Exception on invalid regular expression
     */
    protected function _composeRegexp($search)
    {
        // If valid regexp entered - do nothing
        if (@preg_match($search, '') !== false) {
            return $search;
        }

        // Find out - whether user wanted to enter regexp or normal string.
        if ($this->_isRegexp($search)) {
            Mage::throwException(__('Invalid regular expression: "%1".', $search));
        }

        return '/' . preg_quote($search, '/') . '/i';
    }

    /**
     * Checks search string, whether it was intended to be a regexp or normal search string
     *
     * @param string $search
     * @return bool
     */
    protected function _isRegexp($search)
    {
        if (strlen($search) < 3) {
            return false;
        }

        $possibleDelimiters = '/#~%'; // Limit delimiters to reduce possibility, that we miss string with regexp.

        // Starts with a delimiter
        if (strpos($possibleDelimiters, $search[0]) !== false) {
            return true;
        }

        // Ends with a delimiter and (possible) modifiers
        $pattern = '/[' . preg_quote($possibleDelimiters, '/') . '][imsxeADSUXJu]*$/';
        if (preg_match($pattern, $search)) {
            return true;
        }

        return false;
    }
}
