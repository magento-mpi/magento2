<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Cache_TypeList implements Magento_Core_Model_Cache_TypeListInterface
{
    const INVALIDATED_TYPES = 'core_cache_invalidate';

    /**
     * @var Magento_Core_Model_Cache_Config
     */
    protected $_config;

    /**
     * @var Magento_Core_Model_Cache_InstanceFactory
     */
    protected $_factory;

    /**
     * @var Magento_Core_Model_Cache_StateInterface
     */
    protected $_cacheState;

    /**
     * @var Magento_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * @param Magento_Core_Model_Cache_Config $config
     * @param Magento_Core_Model_Cache_StateInterface $cacheState
     * @param Magento_Core_Model_Cache_InstanceFactory $factory
     * @param Magento_Core_Model_CacheInterface $cache
     */
    public function __construct(
        Magento_Core_Model_Cache_Config $config,
        Magento_Core_Model_Cache_StateInterface $cacheState,
        Magento_Core_Model_Cache_InstanceFactory $factory,
        Magento_Core_Model_CacheInterface $cache
    ) {
        $this->_config = $config;
        $this->_factory = $factory;
        $this->_cacheState = $cacheState;
        $this->_cache = $cache;
    }

    /**
     * Get cache class by cache type from configuration
     *
     * @param string $type
     * @return \Magento\Cache\FrontendInterface
     * @throws UnexpectedValueException
     */
    protected function _getTypeInstance($type)
    {
        $config = $this->_config->getType($type);
        if (!isset($config['instance'])) {
            return null;
        }
        return $this->_factory->get($config['instance']);
    }

    /**
     * Get invalidate types codes
     *
     * @return array
     */
    protected function _getInvalidatedTypes()
    {
        $types = $this->_cache->load(self::INVALIDATED_TYPES);
        if ($types) {
            $types = unserialize($types);
        } else {
            $types = array();
        }
        return $types;
    }

    /**
     * Save invalidated cache types
     *
     * @param array $types
     */
    protected function _saveInvalidatedTypes($types)
    {
        $this->_cache->save(serialize($types), self::INVALIDATED_TYPES);
    }

    /**
     * Get information about all declared cache types
     *
     * @return array
     */
    public function getTypes()
    {
        $types = array();
        $config = $this->_config->getTypes();

        foreach ($config as $type => $node) {
            $typeInstance = $this->_getTypeInstance($type);
            if ($typeInstance instanceof \Magento\Cache\Frontend\Decorator\TagScope) {
                $typeTags = $typeInstance->getTag();
            } else {
                $typeTags = '';
            }
            $types[$type] = new \Magento\Object(array(
                'id'            => $type,
                'cache_type'    => $node['label'],
                'description'   => $node['description'],
                'tags'          => $typeTags,
                'status'        => (int)$this->_cacheState->isEnabled($type),
            ));
        }
        return $types;
    }

    /**
     * Get array of all invalidated cache types
     *
     * @return array
     */
    public function getInvalidated()
    {
        $invalidatedTypes = array();
        $types = $this->_getInvalidatedTypes();
        if ($types) {
            $allTypes = $this->getTypes();
            foreach (array_keys($types) as $type) {
                if (isset($allTypes[$type]) && $this->_cacheState->isEnabled($type)) {
                    $invalidatedTypes[$type] = $allTypes[$type];
                }
            }
        }
        return $invalidatedTypes;
    }

    /**
     * Mark specific cache type(s) as invalidated
     *
     * @param string|array $typeCode
     */
    public function invalidate($typeCode)
    {
        $types = $this->_getInvalidatedTypes();
        if (!is_array($typeCode)) {
            $typeCode = array($typeCode);
        }
        foreach ($typeCode as $code) {
            $types[$code] = 1;
        }
        $this->_saveInvalidatedTypes($types);
    }

    /**
     * Clean cached data for specific cache type
     *
     * @param string $typeCode
     */
    public function cleanType($typeCode)
    {
        $this->_getTypeInstance($typeCode)->clean();
        $types = $this->_getInvalidatedTypes();
        unset($types[$typeCode]);
        $this->_saveInvalidatedTypes($types);
    }
}
