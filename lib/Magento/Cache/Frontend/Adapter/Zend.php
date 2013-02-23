<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adapter for Magento -> Zend cache frontend interfaces
 */
class Magento_Cache_Frontend_Adapter_Zend implements Magento_Cache_FrontendInterface
{
    /**
     * @var Zend_Cache_Core
     */
    protected $_frontend;

    /**
     * @param Zend_Cache_Core $frontend
     */
    public function __construct(Zend_Cache_Core $frontend)
    {
        $this->_frontend = $frontend;
    }

    /**
     * Retrieve single unified identifier
     *
     * @param string $id
     * @return string
     */
    protected function _unifyIdentifier($id)
    {
        return strtoupper($id);
    }

    /**
     * Retrieve multiple unified identifiers
     *
     * @param array $ids
     * @return array
     */
    protected function _unifyIdentifiers(array $ids)
    {
        foreach ($ids as $key => $value) {
            $ids[$key] = $this->_unifyIdentifier($value);
        }
        return $ids;
    }

    /**
     * {@inheritdoc}
     */
    public function test($id)
    {
        return $this->_frontend->test($this->_unifyIdentifier($id));
    }

    /**
     * {@inheritdoc}
     */
    public function load($id)
    {
        return $this->_frontend->load($this->_unifyIdentifier($id));
    }

    /**
     * {@inheritdoc}
     */
    public function save($data, $id, array $tags = array(), $lifeTime = null)
    {
        return $this->_frontend->save($data, $this->_unifyIdentifier($id), $this->_unifyIdentifiers($tags), $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        return $this->_frontend->remove($this->_unifyIdentifier($id));
    }

    /**
     * {@inheritdoc}
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, array $tags = array())
    {
        return $this->_frontend->clean($mode, $this->_unifyIdentifiers($tags));
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return $this->_frontend->clean();
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
        return $this->_frontend;
    }
}
