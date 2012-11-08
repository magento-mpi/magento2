<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Theme_Validator
{
    /**
     * Validators list by data key
     *
     * array('dataKey' => array('validator_name' => [validators], ...), ...)
     *
     * @var array
     */
    protected $_dataValidators = array();

    /**
     * List of errors after validation process
     *
     * array('dataKey' => 'Error message')
     *
     * @var array
     */
    protected $_errorMessages = array();

    /**
     * Initialize validators
     */
    public function __construct()
    {
        $this->_setThemeValidators();
    }

    /**
     * Set default theme validators
     *
     * @return Mage_Core_Model_Theme_Validator
     */
    protected function _setThemeValidators()
    {
        $helper = Mage::helper('Mage_Core_Helper_Data');

        $versionValidators = array(
            array('name' => 'not_empty', 'class' => 'Zend_Validate_NotEmpty', 'break' => true, 'options' => array(),
                  'message' => $helper->__('Field can\'t be empty')),
            array('name' => 'available', 'class' => 'Zend_Validate_Regex', 'break' => true,
                  'options' => array('pattern' => '/^(\d+\.\d+\.\d+\.\d+(\-[a-zA-Z0-9]+)?)$|^\*$/'),
                  'message' => $helper->__('Theme version has not compatible format'))
        );

        $this->addDataValidators('theme_version', $versionValidators)
            ->addDataValidators('magento_version_to', $versionValidators)
            ->addDataValidators('magento_version_from', $versionValidators);

        return $this;
    }

    /**
     * Add validators
     *
     * @param string $dataKey
     * @param array $validators
     * @return Mage_Core_Model_Theme_Validator
     */
    public function addDataValidators($dataKey, $validators)
    {
        if (!isset($this->_dataValidators[$dataKey])) {
            $this->_dataValidators[$dataKey] = array();
        }
        foreach ($validators as $validator) {
            $this->_dataValidators[$dataKey][$validator['name']] = $validator;
        }
        return $this;
    }

    /**
     * Return error messages for items
     *
     * @param string|null $dataKey
     * @return array
     */
    public function getErrorMessages($dataKey = null)
    {
        if ($dataKey) {
            return isset($this->_errorMessages[$dataKey]) ? $this->_errorMessages[$dataKey] : array();
        }
        return $this->_errorMessages;
    }

    /**
     * Instantiate class validator
     *
     * @param array $validators
     * @return Mage_Core_Model_Theme_Validator
     */
    protected function _instantiateValidators(array &$validators)
    {
        foreach ($validators as &$validator) {
            if (is_string($validator['class'])) {
                $validator['class'] = new $validator['class']($validator['options']);
                $validator['class']->setDisableTranslator(true);
            }
        }
        return $this;
    }

    /**
     * Validate one data item
     *
     * @param array $validator
     * @param string $dataKey
     * @param mixed $dataValue
     * @return bool
     */
    protected function _validateDataItem($validator, $dataKey, $dataValue)
    {
        if ($validator['class'] instanceof Zend_Validate_NotEmpty && !$validator['class']->isValid($dataValue)
            || !empty($dataValue) && !$validator['class']->isValid($dataValue)
        ) {
            $this->_errorMessages[$dataKey][] = $validator['message'];
            if ($validator['break']) {
                return false;
            }
        }
        return  true;
    }

    /**
     * Validate all data items
     *
     * @param Varien_Object $data
     * @return bool
     */
    public function validate(Varien_Object $data)
    {
        foreach ($this->_dataValidators as $dataKey => $validators) {
            if (!isset($data[$dataKey]) || !$data->dataHasChangedFor($dataKey)) {
                continue;
            }

            $this->_instantiateValidators($validators);
            foreach ($validators as $validator) {
                if (!$this->_validateDataItem($validator, $dataKey, $data[$dataKey])) {
                    break;
                }
            }
        }
        return empty($this->_errorMessages);
    }
}
