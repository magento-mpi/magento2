<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Cache\Frontend;

/**
 * In-memory readonly pool of all cache front-end instances known to the system
 */
class Pool implements \Iterator
{
    /**
     * Frontend identifier associated with the default settings
     */
    const DEFAULT_FRONTEND_ID = 'generic';

    /**
     * @var \Magento\App\Config
     */
    private $_config;

    /**
     * @var \Magento\App\Cache\Frontend\Factory
     */
    private $_factory;

    /**
     * @var \Magento\Cache\FrontendInterface[]
     */
    private $_instances;

    /**
     * @var array
     */
    private $_settings;

    /**
     * @param \Magento\App\Config $config
     * @param \Magento\App\Cache\Frontend\Factory $frontendFactory
     * @param array $defaultSettings
     * @param array $advancedSettings Format: array('<frontend_id>' => array(<cache_settings>), ...)
     */
    public function __construct(
        \Magento\App\Config $config,
        \Magento\App\Cache\Frontend\Factory $frontendFactory,
        array $defaultSettings = array(),
        array $advancedSettings = array()
    ) {
        $this->_config = $config;
        $this->_factory = $frontendFactory;
        $this->_settings = array(self::DEFAULT_FRONTEND_ID => $defaultSettings) + $advancedSettings;
    }

    /**
     * Create instances of every cache frontend known to the system.
     * Method is to be used for delayed initialization of the iterator.
     */
    protected function _initialize()
    {
        if ($this->_instances === null) {
            $this->_instances = array();
            foreach ($this->_getCacheSettings() as $frontendId => $frontendOptions) {
                $this->_instances[$frontendId] = $this->_factory->create($frontendOptions);
            }
        }
    }

    /**
     * Retrieve settings for all cache front-ends known to the system
     *
     * @return array
     */
    protected function _getCacheSettings()
    {
        /*
         * Merging is intentionally implemented through array_merge() instead of array_replace_recursive()
         * to avoid "inheritance" of the default settings that become irrelevant as soon as cache storage type changes
         */
        return array_merge($this->_settings, $this->_config->getCacheSettings());
    }

    /**
     * {@inheritdoc}
     *
     * @return \Magento\Cache\FrontendInterface
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
     * @return \Magento\Cache\FrontendInterface Cache frontend instance
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
