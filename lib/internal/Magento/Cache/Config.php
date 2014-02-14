<?php
/**
 * Cache configuration model. Provides cache configuration data to the application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cache;

class Config implements ConfigInterface
{
    /**
     * @var \Magento\Cache\Config\Data
     */
    protected $_dataStorage;

    /**
     * @param \Magento\Cache\Config\Data $dataStorage
     */
    public function __construct(\Magento\Cache\Config\Data $dataStorage)
    {
        $this->_dataStorage = $dataStorage;
    }

    /**
     * {inheritdoc}
     *
     * @return array
     */
    public function getTypes()
    {
        return $this->_dataStorage->get('types', array());
    }

    /**
     * {inheritdoc}
     *
     * @param string $type
     * @return array
     */
    public function getType($type)
    {
        return $this->_dataStorage->get('types/' . $type, array());
    }
}
