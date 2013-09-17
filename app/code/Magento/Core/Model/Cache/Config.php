<?php
/**
 * Cache configuration model. Provides cache configuration data to the application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Cache_Config
{
    /**
     * @var Magento_Core_Model_Cache_Config_Data
     */
    protected $_dataStorage;

    /**
     * @param Magento_Core_Model_Cache_Config_Data $dataStorage
     */
    public function __construct(Magento_Core_Model_Cache_Config_Data $dataStorage)
    {
        $this->_dataStorage = $dataStorage;
    }

    /**
     * Get configuration of all cache types
     *
     * @return array
     */
    public function getTypes()
    {
        return $this->_dataStorage->get('types', array());
    }

    /**
     * Get configuration of specified cache type
     *
     * @param string $type
     * @return array
     */
    public function getType($type)
    {
        return $this->_dataStorage->get('types/' . $type, array());
    }
}
