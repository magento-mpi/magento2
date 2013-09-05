<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Theme_Validator
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
    protected $_errorMessages;

    /**
     * Initialize validators
     */
    public function __construct()
    {
        $this->_setVersionValidators();
        $this->_setTypeValidators();
        $this->_setTitleValidators();
    }

    /**
     * Set version validators
     *
     * @return Magento_Core_Model_Theme_Validator
     */
    protected function _setVersionValidators()
    {
        $versionValidators = array(
            array('name' => 'not_empty', 'class' => 'Zend_Validate_NotEmpty', 'break' => true, 'options' => array(),
                  'message' => __('Field can\'t be empty')),
            array('name' => 'available', 'class' => 'Zend_Validate_Regex', 'break' => true,
                  'options' => array('pattern' => '/^(\d+\.\d+\.\d+\.\d+(\-[a-zA-Z0-9]+)?)$|^\*$/'),
                  'message' => __('Theme version has not compatible format'))
        );

        $this->addDataValidators('theme_version', $versionValidators);

        return $this;
    }

    /**
     * Set title validators
     *
     * @return $this
     */
    protected function _setTitleValidators()
    {
        $titleValidators = array(
            array(
                'name' => 'not_empty',
                'class' => 'Zend_Validate_NotEmpty',
                'break' => true,
                'options' => array(),
                'message' => __('Field title can\'t be empty')
            )
        );

        $this->addDataValidators('theme_title', $titleValidators);
        return $this;
    }

    /**
     * Set theme type validators
     *
     * @return Magento_Core_Model_Theme_Validator
     */
    protected function _setTypeValidators()
    {
        $typeValidators = array(
            array(
                'name' => 'not_empty',
                'class' => 'Zend_Validate_NotEmpty',
                'break' => true,
                'options' => array(),
                'message' => __('Field can\'t be empty')
            ),
            array(
                'name' => 'available',
                'class' => 'Zend_Validate_InArray',
                'break' => true,
                'options' => array('haystack' => Magento_Core_Model_Theme::$types),
                'message' => __('Theme type is invalid')
            )
        );

        $this->addDataValidators('type', $typeValidators);

        return $this;
    }

    /**
     * Add validators
     *
     * @param string $dataKey
     * @param array $validators
     * @return Magento_Core_Model_Theme_Validator
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
     * @return Magento_Core_Model_Theme_Validator
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
     * @param \Magento\Object $data
     * @return bool
     */
    public function validate(\Magento\Object $data)
    {
        $this->_errorMessages = array();
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
