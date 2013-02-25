<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cache frontend decorator that enforces marking of cache entries with a tag
 */
class Magento_Cache_Frontend_Decorator_TagMarker implements Magento_Cache_FrontendInterface
{
    /**
     * Cache frontend instance to delegate actual cache operations to
     *
     * @var Magento_Cache_FrontendInterface
     */
    private $_frontend;

    /**
     * Tag to associate cache entries with
     *
     * @var string
     */
    private $_tag;

    /**
     * @param Magento_Cache_FrontendInterface $frontend
     * @param string $tag Cache tag name
     */
    public function __construct(Magento_Cache_FrontendInterface $frontend, $tag)
    {
        $this->_frontend = $frontend;
        $this->_tag = $tag;
    }

    /**
     * Retrieve cache frontend instance being decorated
     *
     * @return Magento_Cache_FrontendInterface
     */
    protected function _getFrontend()
    {
        return $this->_frontend;
    }

    /**
     * Retrieve cache tag name
     *
     * @return string
     */
    public function getTag()
    {
        return $this->_tag;
    }

    /**
     * {@inheritdoc}
     */
    public function test($id)
    {
        return $this->_frontend->test($id);
    }

    /**
     * {@inheritdoc}
     */
    public function load($id)
    {
        return $this->_frontend->load($id);
    }

    /**
     * Enforce marking with a tag
     *
     * {@inheritdoc}
     */
    public function save($data, $id, array $tags = array(), $lifeTime = null)
    {
        $tags[] = $this->_tag;
        return $this->_frontend->save($data, $id, $tags, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        return $this->_frontend->remove($id);
    }

    /**
     * {@inheritdoc}
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, array $tags = array())
    {
        return $this->_frontend->clean($mode, $tags);
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
