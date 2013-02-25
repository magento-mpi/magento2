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
 * In-memory readonly pool of cache front-end instances, specified in the configuration
 */
class Mage_Core_Model_Cache_Frontend_Pool implements Iterator
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
     * @var Magento_Cache_FrontendInterface[]
     */
    private $_instances = array();

    /**
     * Load frontend instances from the configuration
     *
     * @param Mage_Core_Model_Config_Primary $cacheConfig
     * @param Mage_Core_Model_Cache_Frontend_Factory $frontendFactory
     */
    public function __construct(
        Mage_Core_Model_Config_Primary $cacheConfig,
        Mage_Core_Model_Cache_Frontend_Factory $frontendFactory
    ) {
        $frontendNode = $cacheConfig->getNode(self::XML_PATH_SETTINGS_DEFAULT);
        $frontendOptions = $frontendNode ? $frontendNode->asArray() : array();
        $this->_instances[self::DEFAULT_FRONTEND_ID] = $frontendFactory->create($frontendOptions);

        $frontendNodes = $cacheConfig->getNode(self::XML_PATH_SETTINGS_CUSTOM);
        if ($frontendNodes) {
            /** @var $frontendNode Varien_Simplexml_Element */
            foreach ($frontendNodes->children() as $frontendNode) {
                $frontendId = $frontendNode->getName();
                $frontendOptions = $frontendNode->asArray();
                $this->_instances[$frontendId] = $frontendFactory->create($frontendOptions);
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
        return current($this->_instances);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->_instances);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        next($this->_instances);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->_instances);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
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
        if (isset($this->_instances[$identifier])) {
            return $this->_instances[$identifier];
        }
        return null;
    }
}
