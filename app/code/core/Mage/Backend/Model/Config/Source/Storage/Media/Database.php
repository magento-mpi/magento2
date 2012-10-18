<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Generate options for media database selection
 */
class Mage_Backend_Model_Config_Source_Storage_Media_Database
{
    /**
     * Store all detected connections
     *
     * @var array
     */
    protected $_connections = array();

    /**
     * Recursively collect connection configuration
     *
     * @param  string $connectionName
     * @return array
     */
    protected function _collectConnectionConfig($connectionName)
    {
        $config = array();

        if (isset($this->_connections[$connectionName])) {
            $connection = $this->_connections[$connectionName];
            $connection = (array) $connection->descend('connection');

            if (isset($connection['use'])) {
                $config = $this->_collectConnectionConfig((string) $connection['use']);
            }

            $config = array_merge($config, $connection);
        }

        return $config;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $media_storages = array();

        $this->_connections = (array) Mage::app()->getConfig()->getNode('global/resources')->children();
        foreach (array_keys($this->_connections) as $connectionName) {
            $connection = $this->_collectConnectionConfig($connectionName);
            if (!isset($connection['active']) || $connection['active'] != 1) {
                continue;
            }

            $media_storages[] = array('value' => $connectionName, 'label' => $connectionName);
        }
        sort($media_storages);
        reset($media_storages);

        return $media_storages;
    }

}
