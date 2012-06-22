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
 * Stub system config form block for integration test
 */
class Mage_Adminhtml_Block_System_Config_FormStub extends Mage_Adminhtml_Block_System_Config_Form
{
    /**
     * @var array
     */
    protected $_configDataStub = array();

    /**
     * Sets stub config data
     *
     * @param array $configData
     * @return void
     */
    public function setStubConfigData(array $configData = array())
    {
        $this->_configDataStub = $configData;
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
