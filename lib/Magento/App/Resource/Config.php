<?php
/**
 * Resource configuration. Uses application configuration to retrieve resource connection information.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */ 
namespace Magento\App\Resource;

class Config extends \Magento\Config\Data\Scoped
    implements \Magento\App\Resource\ConfigInterface
{
    const DEFAULT_SETUP_CONNECTION = 'default';

    const PARAM_INITIAL_RESOURCES = 'resource';
    /**
     * List of connection names per resource
     *
     * @var array
     */
    protected $_connectionNames = array();

    protected $_scopePriorityScheme = array('global');

    /**
     * @param Config\Reader $reader
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     * @param array $initialResources
     * @throws \InvalidArgumentException
     */
    public function __construct(
        Config\Reader $reader,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Config\CacheInterface $cache,
        $cacheId = 'resourcesCache',
        $initialResources = array()
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
        foreach ($initialResources as $resourceName => $resourceData) {
            if (!isset($resourceData['connection'])) {
                throw new \InvalidArgumentException('Invalid initial resource configuration');
            }
            $this->_connectionNames[$resourceName] = $resourceData['connection'];
        }
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
                } elseif (isset($this->_connectionNames[$pointerResourceName])) {
                    $this->_connectionNames[$resourceName] = $this->_connectionNames[$pointerResourceName];
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
