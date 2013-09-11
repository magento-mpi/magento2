<?php
/**
 * Cache configuration model. Provides cache configuration data to the application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Cache;

class Config
{
    /**
     * @var \Magento\Core\Model\Cache\Config\Data
     */
    protected $_dataStorage;

    /**
     * @param \Magento\Core\Model\Cache\Config\Data $dataStorage
     */
    public function __construct(\Magento\Core\Model\Cache\Config\Data $dataStorage)
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
