<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Element_Field
    extends Mage_Backend_Model_Config_Structure_ElementAbstract
{
    /**
     * Check whether field has backend model
     *
     * @return bool
     */
    public function hasBackendModel()
    {
        return array_key_exists('backend_model', $this->_data) && $this->_data['backend_model'];
    }

    /**
     * Retrieve backend model
     *
     * @return mixed
     */
    public function getBackendModel()
    {
        /* @var $dataObject Mage_Core_Model_Config_Data */
        $dataObject = $this->_objectFactory->getModelInstance($this->_data['backend_model']);
        if (!$dataObject instanceof Mage_Core_Model_Config_Data) {
            Mage::throwException('Invalid config field backend model: ' . $this->_data['backend_model']);
        }
    }

    /**
     * Retrieve element config path
     *
     * @return string|null
     */
    public function getConfigPath()
    {
        return isset($fieldConfig['config_path']) ? $fieldConfig['config_path'] : null;
    }

    /**
     * Check whether field should be shown in default scope
     *
     * @return bool
     */
    public function showInDefault()
    {
        return isset($fieldConfig['showInDefault']) && (int)$fieldConfig['showInDefault'];
    }
}
