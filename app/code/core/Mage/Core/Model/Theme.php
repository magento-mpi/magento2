<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme model class
 */
class Mage_Core_Model_Theme extends Mage_Core_Model_Abstract
{
    /**
     * Version validator
     */
    const VERSION_VALIDATOR = '/^\d+\.\d+\.\d+\.\d+(\-[\w\d]+)?$|\*/';

    /**
     * Parent theme validator
     */
    const PARENT_THEME_VALIDATOR = '(^$|([^\/]+\/)?[^\/]+|^$)';

    /**
     * Code validator
     */
    const CODE_VALIDATOR = '/^[a-z]+[a-z0-9_]+$/';

    /**
     * Regular expression validators
     *
     * @var array
     */
    protected $_regExpValidators = array(
        'package_code'         => self::CODE_VALIDATOR,
        'theme_code'           => self::CODE_VALIDATOR,
        'parent_theme'         => self::PARENT_THEME_VALIDATOR,
        'theme_version'        => self::VERSION_VALIDATOR,
        'magento_version_from' => self::VERSION_VALIDATOR,
        'magento_version_to'   => self::VERSION_VALIDATOR,
    );

    /**
     * Theme model initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Resource_Theme');
    }

    /**
     * Validate theme data
     *
     * @return Mage_Core_Model_Theme
     */
    public function validateData()
    {
        $this->_regExpValidator();
        return $this;
    }

    /**
     * Validate fields using regular expression
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _regExpValidator()
    {
        foreach ($this->_regExpValidators as $field => $validator) {
            if (!preg_match($validator, $this->getData($field))) {
                Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__(
                    'Invalid field data: %s.', $field
                ));
            }
        }
        return $this;
    }

    /**
     * Before save
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _beforeSave()
    {
        $this->validateData();
        parent::_beforeSave();
        return $this;
    }
}
