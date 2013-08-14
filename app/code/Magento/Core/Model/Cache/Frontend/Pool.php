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
 * In-memory readonly pool of cache front-end instances, specified in the configuration
 */
class Magento_Core_Model_Cache_Frontend_Pool implements Iterator
{
    /**#@+
     * XPaths where cache frontend settings reside
     */
    const XML_PATH_SETTINGS_DEFAULT = 'global/cache';
    const XML_PATH_SETTINGS_CUSTOM  = 'global/cache_advanced';
    /**#@-*/

    /**
     * Frontend identifier associated with the default settings
     */
    const DEFAULT_FRONTEND_ID = 'generic';

    /**
     * @var Magento_Core_Model_ConfigInterface
     */
    private $_config;

    /**
     * @var Magento_Core_Model_Cache_Frontend_Factory
     */
    private $_factory;

    /**
     * @var Magento_Cache_FrontendInterface[]
     */
    private $_instances;

    /**
     * Store references to objects, necessary to perform delayed initialization
     *
     * @param Magento_Core_Model_Config_Primary $cacheConfig
     * @param Magento_Core_Model_Cache_Frontend_Factory $frontendFactory
     */
    public function __construct(
        Magento_Core_Model_Config_Primary $cacheConfig,
        Magento_Core_Model_Cache_Frontend_Factory $frontendFactory
    ) {
        $this->_config = $cacheConfig;
        $this->_factory = $frontendFactory;
    }

    /**
     * Load frontend instances from the configuration, to be used for delayed initialization
     */
    protected function _initialize()
    {
        if ($this->_instances === null) {
            $this->_instances = array();
            // default front-end
            $frontendNode = $this->_config->getNode(self::XML_PATH_SETTINGS_DEFAULT);
            $frontendOptions = $frontendNode ? $frontendNode->asArray() : array();
            $this->_instances[self::DEFAULT_FRONTEND_ID] = $this->_factory->create($frontendOptions);
            // additional front-ends
            $frontendNodes = $this->_config->getNode(self::XML_PATH_SETTINGS_CUSTOM);
            if ($frontendNodes) {
                /** @var $frontendNode Magento_Simplexml_Element */
                foreach ($frontendNodes->children() as $frontendNode) {
                    $frontendId = $frontendNode->getName();
                    $frontendOptions = $frontendNode->asArray();
                    $this->_instances[$frontendId] = $this->_factory->create($frontendOptions);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return Magento_Cache_FrontendInterface
     */
    public function current()
    {
        $this->_initialize();
        return current($this->_instances);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        $this->_initialize();
        return key($this->_instances);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->_initialize();
        next($this->_instances);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->_initialize();
        reset($this->_instances);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        $this->_initialize();
        return (bool)current($this->_instances);
    }

    /**
     * Retrieve frontend instance by its unique identifier, or return NULL, if identifier is not recognized
     *
     * @param string $identifier Cache frontend identifier
     * @return Magento_Cache_FrontendInterface Cache frontend instance
     */
    public function get($identifier)
    {
        $this->_initialize();
        if (isset($this->_instances[$identifier])) {
            return $this->_instances[$identifier];
        }
        return null;
    }
}
