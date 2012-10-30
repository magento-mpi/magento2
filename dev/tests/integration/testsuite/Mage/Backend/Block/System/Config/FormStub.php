<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Stub system config form block for integration test
 */
class Mage_Backend_Block_System_Config_FormStub extends Mage_Backend_Block_System_Config_Form
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
     * @return Mage_Backend_Block_System_Config_Form
     */
    protected function _initObjects()
    {
        parent::_initObjects();
        $this->_configData = $this->_configDataStub;
        $this->_defaultFieldRenderer = Mage::app()->getLayout()->createBlock(
            'Mage_Backend_Block_System_Config_Form_Field'
        );
    }
}
