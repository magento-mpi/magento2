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
 * Proxy that delegates execution to an original cache type instance, if access is allowed at the moment.
 * It's typical for "access proxies" to have a decorator-like implementation, the difference is logical -
 * controlling access rather than attaching additional responsibility to a subject.
 */
class Magento_Core_Model_Cache_Type_AccessProxy extends Magento_Cache_Frontend_Decorator_Bare
{
    /**
     * Cache types manager
     *
     * @var Magento_Core_Model_Cache_Types
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
     * @param Magento_Core_Model_Cache_Types $cacheTypes
     * @param string $identifier Cache type identifier
     */
    public function __construct(
        Magento_Cache_FrontendInterface $frontend,
        Magento_Core_Model_Cache_Types $cacheTypes,
        $identifier
    ) {
        parent::__construct($frontend);
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
    public function test($identifier)
    {
        if (!$this->_isEnabled()) {
            return false;
        }
        return parent::test($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function load($identifier)
    {
        if (!$this->_isEnabled()) {
            return false;
        }
        return parent::load($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function save($data, $identifier, array $tags = array(), $lifeTime = null)
    {
        if (!$this->_isEnabled()) {
            return true;
        }
        return parent::save($data, $identifier, $tags, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($identifier)
    {
        if (!$this->_isEnabled()) {
            return true;
        }
        return parent::remove($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, array $tags = array())
    {
        if (!$this->_isEnabled()) {
            return true;
        }
        return parent::clean($mode, $tags);
    }
}
