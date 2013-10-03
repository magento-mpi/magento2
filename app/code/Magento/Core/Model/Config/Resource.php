<?php
/**
 * Resource configuration. Uses application configuration to retrieve resource connection information.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */ 
namespace Magento\Core\Model\Config;

class Resource extends \Magento\Config\Data\Scoped
    implements \Magento\Core\Model\Config\ResourceInterface
{
    const DEFAULT_READ_CONNECTION  = 'read';
    const DEFAULT_WRITE_CONNECTION = 'write';
    const DEFAULT_SETUP_CONNECTION = 'default';

    /**
     * @param \Magento\Core\Model\Resource\Config\Reader $reader
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Core\Model\Resource\Config\Reader $reader,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Config\CacheInterface $cache,
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
