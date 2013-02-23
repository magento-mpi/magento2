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
 * Proxy that delegates execution to an original cache type instance, if access is allowed at the moment
 */
class Mage_Core_Model_Cache_Type_AccessProxy implements Magento_Cache_FrontendInterface
{
    /**
     * Cache type instance to delegate actual cache operations to
     *
     * @var Magento_Cache_FrontendInterface
     */
    private $_frontend;

    /**
     * Cache types manager
     *
     * @var Mage_Core_Model_Cache_Types
     */
    private $_cacheTypes;

    /**
     * Cache type identifier
     *
     * @var string
     */
    private $_identifier;

    /**
     * @param Magento_Cache_FrontendInterface $frontend
     * @param Mage_Core_Model_Cache_Types $cacheTypes
     * @param string $identifier Cache type identifier
     */
    public function __construct(
        Magento_Cache_FrontendInterface $frontend,
        Mage_Core_Model_Cache_Types $cacheTypes,
        $identifier
    ) {
        $this->_frontend = $frontend;
        $this->_cacheTypes = $cacheTypes;
        $this->_identifier = $identifier;
    }

    /**
     * Whether a cache type is enabled at the moment or not
     *
     * @return bool
     */
    protected function _isEnabled()
    {
        return $this->_cacheTypes->isEnabled($this->_identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function test($id)
    {
        if (!$this->_isEnabled()) {
            return false;
        }
        return $this->_frontend->test($id);
    }

    /**
     * {@inheritdoc}
     */
    public function load($id)
    {
        if (!$this->_isEnabled()) {
            return false;
        }
        return $this->_frontend->load($id);
    }

    /**
     * {@inheritdoc}
     */
    public function save($data, $id, array $tags = array(), $lifeTime = null)
    {
        if (!$this->_isEnabled()) {
            return true;
        }
        return $this->_frontend->save($data, $id, $tags, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        if (!$this->_isEnabled()) {
            return true;
        }
        return $this->_frontend->remove($id);
    }

    /**
     * {@inheritdoc}
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, array $tags = array())
    {
        if (!$this->_isEnabled()) {
            return true;
        }
        return $this->_frontend->clean($mode, $tags);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        if (!$this->_isEnabled()) {
            return true;
        }
        return $this->_frontend->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getBackend()
    {
        return $this->_frontend->getBackend();
    }

    /**
     * {@inheritdoc}
     */
    public function getLowLevelFrontend()
    {
        return $this->_frontend->getLowLevelFrontend();
    }
}
