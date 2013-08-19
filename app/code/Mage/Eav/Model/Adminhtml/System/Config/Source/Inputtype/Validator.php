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
 * Validator for check input type value
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator extends Zend_Validate_InArray
{

    /**
     * Construct
     */
    public function __construct()
    {
        //set data haystack
        /** @var $helper Mage_Eav_Helper_Data */
        $helper = Mage::helper('Mage_Eav_Helper_Data');
        $haystack = $helper->getInputTypesValidatorData();

        //reset message template and set custom
        $this->_messageTemplates = null;
        $this->_initMessageTemplates();

        //parent construct with options
        parent::__construct(array(
             'haystack' => $haystack,
             'strict'   => true,
        ));
    }

    /**
     * Initialize message templates with translating
     *
     * @return Mage_Adminhtml_Model_Core_File_Validator_SavePath_Available
     */
    protected function _initMessageTemplates()
    {
        if (!$this->_messageTemplates) {
            $this->_messageTemplates = array(
                self::NOT_IN_ARRAY =>
                    __('Input type "%value%" not found in the input types list.'),
            );
        }
        return $this;
    }

    /**
     * Add input type to haystack
     *
     * @param string $type
     * @return Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator
     */
    public function addInputType($type)
    {
        if (!in_array((string) $type, $this->_haystack, true)) {
            $this->_haystack[] = (string) $type;
        }
        return $this;
    }
}
