<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Fieldset_Config
{
    /**
     * @var Magento_Core_Model_Fieldset_Config_Data
     */
    protected $_dataStorage;

    /**
     * @param Magento_Core_Model_Fieldset_Config_Data $dataStorage
     */
    public function __construct(Magento_Core_Model_Fieldset_Config_Data $dataStorage)
    {
        $this->_dataStorage = $dataStorage;
    }

    /**
     * Get fieldsets by $path
     *
     * @param string $path
     * @return array
     */
    public function getFieldsets($path)
    {
        return $this->_dataStorage->get($path);
    }

    /**
     * Get the fieldset for an area
     *
     * @param string $name fieldset name
     * @param string $root fieldset area, could be 'admin'
     * @return null|array
     */
    public function getFieldset($name, $root = 'global')
    {
        $fieldsets = $this->getFieldsets($root);
        if (empty($fieldsets)) {
            return null;
        }
        return isset($fieldsets[$name]) ? $fieldsets[$name] : null;
    }
}
