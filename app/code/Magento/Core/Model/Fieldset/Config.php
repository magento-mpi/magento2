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
     * @return array
     */
    public function getFieldsets()
    {
        return $this->_dataStorage->get();
    }
}
