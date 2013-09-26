<?php
/**
 * Resource configuration. Uses application configuration to retrieve resource connection information.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */ 
class Magento_Core_Model_Config_Resource extends Magento_Config_Data_Scoped
    implements Magento_Core_Model_Config_ResourceInterface
{
    const DEFAULT_READ_CONNECTION  = 'read';
    const DEFAULT_WRITE_CONNECTION = 'write';
    const DEFAULT_SETUP_CONNECTION = 'default';

    /**
     * @param Magento_Core_Model_Resource_Config_Reader $reader
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_Core_Model_Resource_Config_Reader $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Config_CacheInterface $cache,
        $cacheId = 'resourcesCache'
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
    }

    /**
     * Retrieve resource connection instance name
     *
     * @param string $resourceName
     * @return string
     */
    public function getConnectionName($resourceName)
    {
        $connectionName = self::DEFAULT_SETUP_CONNECTION;

        if (!isset($this->_connectionNames[$resourceName])) {

            $resourcesConfig = $this->get();
            $pointerResourceName = $resourceName;
            while (true) {
                if (isset($resourcesConfig[$pointerResourceName]['connection'])) {
                    $connectionName = $resourcesConfig[$pointerResourceName]['connection'];
                    $this->_connectionNames[$resourceName] = $connectionName;
                    break;
                } elseif (isset($resourcesConfig[$pointerResourceName]['extends'])) {
                    $pointerResourceName = $resourcesConfig[$pointerResourceName]['extends'];
                } else {
                    break;
                }
            }
        } else {
            $connectionName = $this->_connectionNames[$resourceName];
        }

        return $connectionName;
    }
}
