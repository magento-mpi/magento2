<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System cache model
 * support id and tags preffix support,
 */

class Magento_Core_Model_Cache implements Magento_Core_Model_CacheInterface
{
    const INVALIDATED_TYPES = 'core_cache_invalidate';
    const XML_PATH_TYPES    = 'global/cache/types';

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Magento_Core_Model_Factory_Helper
     */
    protected $_helperFactory;

    /**
     * @var string
     */
    protected $_frontendIdentifier = Magento_Core_Model_Cache_Frontend_Pool::DEFAULT_FRONTEND_ID;

    /**
     * @var Magento_Core_Model_Cache_Frontend_Pool
     */
    protected $_frontendPool;

    /**
     * Cache frontend API
     *
     * @var Magento_Cache_FrontendInterface
     */
    protected $_frontend;

    /**
     * @var Magento_Core_Model_Cache_Types
     */
    private $_cacheTypes;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_Cache_Frontend_Pool $frontendPool
     * @param Magento_Core_Model_Cache_Types $cacheTypes
     * @param Magento_Core_Model_ConfigInterface $config
     * @param Magento_Core_Model_Dir $dirs
     * @param Magento_Core_Model_Factory_Helper $helperFactory
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_Cache_Frontend_Pool $frontendPool,
        Magento_Core_Model_Cache_Types $cacheTypes,
        Magento_Core_Model_ConfigInterface $config,
        Magento_Core_Model_Dir $dirs,
        Magento_Core_Model_Factory_Helper $helperFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_frontendPool = $frontendPool;
        $this->_frontend = $frontendPool->get($this->_frontendIdentifier);
        $this->_cacheTypes = $cacheTypes;
        $this->_config = $config;
        $this->_helperFactory = $helperFactory;
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
     * @param  string $identifier
     * @return string
     */
    public function load($identifier)
    {
        return $this->_frontend->load($identifier);
    }

    /**
     * Save data
     *
     * @param string $data
     * @param string $identifier
     * @param array $tags
     * @param int $lifeTime
     * @return bool
     */
    public function save($data, $identifier, $tags=array(), $lifeTime=null)
    {
        return $this->_frontend->save((string)$data, $identifier, $tags, $lifeTime);
    }

    /**
     * Remove cached data by identifier
     *
     * @param string $identifier
     * @return bool
     */
    public function remove($identifier)
    {
        return $this->_frontend->remove($identifier);
    }

    /**
     * Clean cached data by specific tag
     *
     * @param array $tags
     * @return bool
     */
    public function clean($tags = array())
    {
        if ($tags) {
            $result = $this->_frontend->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, (array)$tags);
        } else {
            /** @deprecated special case of cleaning by empty tags is deprecated after 2.0.0.0-dev42 */
            $result = false;
            /** @var $cacheFrontend Magento_Cache_FrontendInterface */
            foreach ($this->_frontendPool as $cacheFrontend) {
                if ($cacheFrontend->clean()) {
                    $result = true;
                }
            }
        }
        return $result;
    }

    /**
     * Check if cache can be used for specific data type
     *
     * @param string $typeCode
     * @return bool
     * @deprecated deprecated after 2.0.0.0-dev42 in favour of Magento_Core_Model_Cache_Types::isEnabled()
     */
    public function canUse($typeCode)
    {
        return $this->_cacheTypes->isEnabled($typeCode);
    }

    /**
     * Disable cache usage for specific data type
     *
     * @param string $typeCode
     * @return Magento_Core_Model_CacheInterface
     * @deprecated deprecated after 2.0.0.0-dev42 in favour of Magento_Core_Model_Cache_Types::setEnabled()
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
     * @return Magento_Core_Model_CacheInterface
     * @deprecated deprecated after 2.0.0.0-dev42 in favour of Magento_Core_Model_Cache_Types::setEnabled()
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
            /** @var $helper Magento_Core_Helper_Data*/
            $helper = $this->_helperFactory->get('Magento_Core_Helper_Data');
            foreach ($config->children() as $type => $node) {
                $typeInstance = $this->_getTypeInstance($type);
                if ($typeInstance instanceof Magento_Cache_Frontend_Decorator_TagScope) {
                    $typeTags = $typeInstance->getTag();
                } else {
                    $typeTags = '';
                }
                $types[$type] = new Magento_Object(array(
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
     * @return Magento_Core_Model_Cache
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
     * @return Magento_Core_Model_CacheInterface
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
     * @return Magento_Core_Model_CacheInterface
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
