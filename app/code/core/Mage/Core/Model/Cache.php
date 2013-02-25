<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System cache model
 * support id and tags preffix support,
 */

class Mage_Core_Model_Cache implements Mage_Core_Model_CacheInterface
{
    const INVALIDATED_TYPES = 'core_cache_invalidate';
    const XML_PATH_TYPES    = 'global/cache/types';

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Mage_Core_Model_Factory_Helper
     */
    protected $_helperFactory;

    /**
     * @var string
     */
    protected $_frontendIdentifier = Mage_Core_Model_Cache_Frontend_Pool::DEFAULT_FRONTEND_ID;

    /**
     * Cache frontend API
     *
     * @var Magento_Cache_FrontendInterface
     */
    protected $_frontend;

    /**
     * @var Mage_Core_Model_Cache_Types
     */
    private $_cacheTypes;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Cache_Frontend_Pool $frontendPool
     * @param Mage_Core_Model_Cache_Types $cacheTypes
     * @param Mage_Core_Model_ConfigInterface $config
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Cache_Frontend_Pool $frontendPool,
        Mage_Core_Model_Cache_Types $cacheTypes,
        Mage_Core_Model_ConfigInterface $config,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Factory_Helper $helperFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_frontend = $frontendPool->get($this->_frontendIdentifier);
        $this->_cacheTypes = $cacheTypes;
        $this->_config = $config;
        $this->_helperFactory = $helperFactory;
    }

    /**
     * Generate Magento Profiler tags
     *
     * @param string $operation
     * @param string $frontendType
     * @param string $backendType
     * @return array
     */
    protected function _generateProfilerTags($operation, $frontendType = '', $backendType = '')
    {
        $profilerTags = array('group' => 'cache',
            'operation' => 'cache:' . $operation);

        if (!empty($frontendType)) {
            $profilerTags['frontend_type'] = $frontendType;
        } elseif ($this->_frontend) {
            $profilerTags['frontend_type'] = get_class($this->_frontend);
        }

        if (!empty($backendType)) {
            $profilerTags['backend_type'] = $backendType;
        } elseif ($this->_frontend) {
            $parsedBackendType = $this->_getBackendType();
            if ($parsedBackendType) {
                $profilerTags['backend_type'] = $parsedBackendType;
            }
        }

        return $profilerTags;
    }

    /**
     * Get cache backend type
     *
     * @return string
     */
    protected function _getBackendType()
    {
        $backendType = '';

        if ($this->_frontend) {
            $backend = $this->_frontend->getBackend();
            $backendClass = get_class($backend);

            $possibleCacheBackends = array('Zend_Cache_Backend_', 'Varien_Cache_Backend_');
            foreach ($possibleCacheBackends as $backendClassStart) {
                if (substr($backendClass, 0, strlen($backendClassStart)) == $backendClassStart) {
                    $backendType = substr($backendClass, strlen($backendClassStart));
                    break;
                }
            }
        }

        return $backendType;
    }

    /**
     * Get cache frontend API object
     *
     * @return Magento_Cache_FrontendInterface
     */
    public function getFrontend()
    {
        return $this->_frontend;
    }

    /**
     * Load data from cache by id
     *
     * @param   string $id
     * @return  string
     */
    public function load($id)
    {
        Magento_Profiler::start('cache_load', $this->_generateProfilerTags('load'));
        $result = $this->_frontend->load($id);
        Magento_Profiler::stop('cache_load');

        return $result;
    }

    /**
     * Save data
     *
     * @param string $data
     * @param string $id
     * @param array $tags
     * @param int $lifeTime
     * @return bool
     */
    public function save($data, $id, $tags=array(), $lifeTime=null)
    {
        /**
         * Add global magento cache tag to all cached data exclude config cache
         */
        if (!in_array(Mage_Core_Model_Config::CACHE_TAG, $tags)) {
            $tags[] = Mage_Core_Model_AppInterface::CACHE_TAG;
        }

        Magento_Profiler::start('cache_save', $this->_generateProfilerTags('save'));
        $result = $this->_frontend->save((string)$data, $id, $tags, $lifeTime);
        Magento_Profiler::stop('cache_save');

        return $result;
    }

    /**
     * Remove cached data by identifier
     *
     * @param string $id
     * @return bool
     */
    public function remove($id)
    {
        Magento_Profiler::start('cache_remove', $this->_generateProfilerTags('remove'));
        $result = $this->_frontend->remove($id);
        Magento_Profiler::stop('cache_remove');

        return $result;
    }

    /**
     * Clean cached data by specific tag
     *
     * @param array $tags
     * @return bool
     */
    public function clean($tags = array())
    {
        Magento_Profiler::start('cache_clean', $this->_generateProfilerTags('clean'));

        $mode = Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG;
        if (!empty($tags)) {
            if (!is_array($tags)) {
                $tags = array($tags);
            }
            $res = $this->_frontend->clean($mode, $tags);
        } else {
            $res = $this->_frontend->clean($mode, array(Mage_Core_Model_AppInterface::CACHE_TAG));
            $res = $res && $this->_frontend->clean($mode, array(Mage_Core_Model_Config::CACHE_TAG));
        }

        Magento_Profiler::stop('cache_clean');

        return $res;
    }

    /**
     * Clean cached data by specific tag
     *
     * @return bool
     */
    public function flush()
    {
        Magento_Profiler::start('cache_flush', $this->_generateProfilerTags('flush'));
        $res = $this->_frontend->clean();
        Magento_Profiler::stop('cache_flush');

        return $res;
    }

    /**
     * Check if cache can be used for specific data type
     *
     * @param string $typeCode
     * @return bool
     * @deprecated deprecated after 2.0.0.0-dev42 in favour of Mage_Core_Model_Cache_Types::isEnabled()
     */
    public function canUse($typeCode)
    {
        return $this->_cacheTypes->isEnabled($typeCode);
    }

    /**
     * Disable cache usage for specific data type
     *
     * @param string $typeCode
     * @return Mage_Core_Model_Cache
     * @deprecated deprecated after 2.0.0.0-dev42 in favour of Mage_Core_Model_Cache_Types::setEnabled()
     */
    public function banUse($typeCode)
    {
        $this->_cacheTypes->setEnabled($typeCode, false);
        return $this;
    }

    /**
     * Enable cache usage for specific data type
     *
     * @param string $typeCode
     * @return Mage_Core_Model_Cache
     * @deprecated deprecated after 2.0.0.0-dev42 in favour of Mage_Core_Model_Cache_Types::setEnabled()
     */
    public function allowUse($typeCode)
    {
        $this->_cacheTypes->setEnabled($typeCode, true);
        return $this;
    }

    /**
     * Get cache class by cache type from configuration
     *
     * @param string $type
     * @return Magento_Cache_FrontendInterface
     * @throws UnexpectedValueException
     */
    protected function _getTypeInstance($type)
    {
        $path = self::XML_PATH_TYPES . '/' . $type . '/class';
        $class = $this->_config->getNode($path);
        if (!$class) {
            return null;
        }
        $class = (string)$class;
        $instance = $this->_objectManager->get($class);
        if (!($instance instanceof Magento_Cache_FrontendInterface)) {
            throw new UnexpectedValueException("Cache type class '$class' has to be a cache frontend.");
        }
        return $instance;
    }

    /**
     * Get information about all declared cache types
     *
     * @return array
     */
    public function getTypes()
    {
        $types = array();
        $config = $this->_config->getNode(self::XML_PATH_TYPES);
        if ($config) {
            /** @var $helper Mage_Core_Helper_Data*/
            $helper = $this->_helperFactory->get('Mage_Core_Helper_Data');
            foreach ($config->children() as $type => $node) {
                $typeInstance = $this->_getTypeInstance($type);
                if ($typeInstance instanceof Magento_Cache_Frontend_TagDecorator) {
                    $typeTags = $typeInstance->getTag();
                } else {
                    $typeTags = '';
                }
                $types[$type] = new Varien_Object(array(
                    'id'            => $type,
                    'cache_type'    => $helper->__((string)$node->label),
                    'description'   => $helper->__((string)$node->description),
                    'tags'          => $typeTags,
                    'status'        => (int)$this->canUse($type),
                ));
            }
        }
        return $types;
    }

    /**
     * Get invalidate types codes
     *
     * @return array
     */
    protected function _getInvalidatedTypes()
    {
        $types = $this->load(self::INVALIDATED_TYPES);
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
     * @return Mage_Core_Model_Cache
     */
    protected function _saveInvalidatedTypes($types)
    {
        $this->save(serialize($types), self::INVALIDATED_TYPES);
        return $this;
    }

    /**
     * Get array of all invalidated cache types
     *
     * @return array
     */
    public function getInvalidatedTypes()
    {
        $invalidatedTypes = array();
        $types = $this->_getInvalidatedTypes();
        if ($types) {
            $allTypes = $this->getTypes();
            foreach ($types as $type => $flag) {
                if (isset($allTypes[$type]) && $this->canUse($type)) {
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
     * @return Mage_Core_Model_Cache
     */
    public function invalidateType($typeCode)
    {
        $types = $this->_getInvalidatedTypes();
        if (!is_array($typeCode)) {
            $typeCode = array($typeCode);
        }
        foreach ($typeCode as $code) {
            $types[$code] = 1;
        }
        $this->_saveInvalidatedTypes($types);
        return $this;
    }

    /**
     * Clean cached data for specific cache type
     *
     * @param string $typeCode
     * @return Mage_Core_Model_Cache
     */
    public function cleanType($typeCode)
    {
        $this->_getTypeInstance($typeCode)->clean();
        $types = $this->_getInvalidatedTypes();
        unset($types[$typeCode]);
        $this->_saveInvalidatedTypes($types);
        return $this;
    }
}
