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
    const PARENT_THEME_VALIDATOR = '(^$|([^\/]+\/)?[^\/]+)';

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
        self::CODE_VALIDATOR => array('package_code', 'theme_code'),
        self::PARENT_THEME_VALIDATOR => array('parent_theme'),
        self::VERSION_VALIDATOR => array('theme_version', 'magento_version_from', 'magento_version_to'),
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
        return $this->_regExpValidator();
    }

    /**
     * Validate fields using regular expression
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _regExpValidator()
    {
        $themeData = $this->getData();
        foreach ($this->_regExpValidators as $validator => $fields) {
            array_walk($fields, function ($field) use ($validator, $themeData) {
                if (!preg_match($validator, $themeData[$field])) {
                    Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__('Invalid field data: %s.', $field));
                }
            });
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
        return parent::_beforeSave();
    }
}
