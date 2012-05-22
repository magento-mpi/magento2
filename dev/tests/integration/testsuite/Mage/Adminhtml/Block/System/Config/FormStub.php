<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Stub System config form block for integration test
 */
class Mage_Adminhtml_Block_System_Config_FormStub extends Mage_Adminhtml_Block_System_Config_Form
{
    /**
     * @var array
     */
    protected $_configDataStub;

    /**
     * Init fieldset fields
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param Varien_Simplexml_Element $group
     * @param Varien_Simplexml_Element $section
     * @param array $configData
     * @param string $fieldPrefix
     * @param string $labelPrefix
     * @return Mage_Adminhtml_Block_System_Config_Form
     */
    public function initFields($fieldset, $group, $section, array $configData = array(), $fieldPrefix = '',
        $labelPrefix = '')
    {
        $this->_configDataStub = $configData;
        parent::initFields($fieldset, $group, $section, $fieldPrefix, $labelPrefix);
        return $this;
    }

    /**
     * Initialize properties of object required for test.
     *
     * @return Mage_Adminhtml_Block_System_Config_Form
     */
    protected function _initObjects()
    {
        parent::_initObjects();
        $this->_configData = $this->_configDataStub;
        $this->_defaultFieldRenderer = new Mage_Adminhtml_Block_System_Config_Form_Field();
    }
}
