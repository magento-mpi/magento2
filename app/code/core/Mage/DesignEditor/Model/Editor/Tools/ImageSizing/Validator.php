<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Image sizing validator
 */
class Mage_DesignEditor_Model_Editor_Tools_ImageSizing_Validator
{
    /**
     * Control type for image white border
     */
    const FIELD_WHITE_BORDER = 'white-border';

    /**
     * Control type for image type
     */
    const FIELD_IMAGE_TYPE = 'image-type';

    /**
     * Control type for image width
     */
    const FIELD_IMAGE_WIDTH = 'image-width';

    /**
     * Control type for image ratio
     */
    const FIELD_IMAGE_RATIO = 'image-ratio';

    /**
     * Control type for image height
     */
    const FIELD_IMAGE_HEIGHT = 'image-height';

    /**
     * Max value for width or height
     */
    const MAX_SIZE_VALUE = 500;

    /**
     * List of allowed filed control types
     *
     * @var array
     */
    protected $_allowedTypes = array(
        self::FIELD_WHITE_BORDER,
        self::FIELD_IMAGE_TYPE,
        self::FIELD_IMAGE_WIDTH,
        self::FIELD_IMAGE_RATIO,
        self::FIELD_IMAGE_HEIGHT
    );

    /**
     * Clean data
     *
     * @var array
     */
    protected $_cleanData = array();

    /**
     * List of controls for validate grouped by type
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * Validate data
     *
     * @param array $controls
     * @param array $data
     * @return array
     */
    public function validate(array $controls, array $data)
    {
        $this->_initFieldByTypes($controls);

        $this->_cleanData = array();
        foreach ($this->_allowedTypes as $type) {
            if (isset($this->_fields[$type])) {
                $validators = $this->_getValidators($type);
                $this->_validate($validators, $this->_fields[$type], $data);
            }
        }
        return $this->_cleanData;
    }

    /**
     * Initialize list of controls for validation
     *
     * @param array $controls
     * @return Mage_DesignEditor_Model_Editor_Tools_ImageSizing_Validator
     */
    protected function _initFieldByTypes(array $controls)
    {
        $this->_fields = array();
        foreach ($controls as $control) {
            foreach ($control['components'] as $name => $component) {
                if (in_array($component['type'], $this->_allowedTypes)) {
                    $this->_fields[$component['type']][] = $name;
                }
            }
        }
        return $this;
    }

    /**
     * Validate fields
     *
     * @param array $validators
     * @param array $fields
     * @param array $data
     * @return Mage_DesignEditor_Model_Editor_Tools_ImageSizing_Validator
     */
    protected function _validate(array $validators, array $fields, array $data)
    {
        foreach ($fields as $field) {
            if (isset($data[$field]) && $this->_validateField($validators, $data[$field])) {
                $this->_cleanData[$field] = $data[$field];
            }
        }
        return $this;
    }

    /**
     * Validate field value
     *
     * @param array $validators
     * @param string $filedData
     * @return bool
     */
    protected function _validateField(array $validators, $filedData)
    {
        /** @var $validator Zend_Validate_Abstract */
        foreach ($validators as $validator) {
            if (!$validator->isValid($filedData)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get validators by type
     *
     * @param string $type
     * @return array
     */
    protected function _getValidators($type)
    {
        $validators = array();
        switch ($type) {
            case self::FIELD_WHITE_BORDER:
                $validators = array(
                    array('class' => 'Zend_Validate_Int', 'options' => array()),
                    array('class' => 'Zend_Validate_Between', 'options' => array(
                        'min' => 0, 'max' => 1, 'inclusive' => true
                )));
                break;
            case self::FIELD_IMAGE_WIDTH:
            case self::FIELD_IMAGE_HEIGHT:
                $validators = array(
                    array('class' => 'Zend_Validate_Regex', 'options' => array('pattern' => '/[0-9]*/')),
                    array('class' => 'Zend_Validate_Between', 'options' => array(
                        'min' => 0, 'max' => self::MAX_SIZE_VALUE, 'inclusive' => true
                )));
                break;
            case self::FIELD_IMAGE_RATIO:
                $validators = array(
                    array('class' => 'Zend_Validate_InArray', 'options' => array('haystack' => array('0', '1'))));
                break;
            case self::FIELD_IMAGE_TYPE:
                $validators = array(
                    array('class' => 'Zend_Validate_InArray', 'options' => array('haystack' => array(
                        'image', 'small_image', 'thumbnail'
                ))));
                break;
        }
        $this->_instantiateValidators($validators);
        return $validators;
    }

    /**
     * Instantiate class validator
     *
     * @param array $validators
     * @return Mage_DesignEditor_Model_Editor_Tools_ImageSizing_Validator
     */
    protected function _instantiateValidators(array &$validators)
    {
        foreach ($validators as &$validator) {
            $validator = new $validator['class']($validator['options']);
            $validator->setDisableTranslator(true);
        }
        return $this;
    }
}
