<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cache frontend decorator that enforces association of cache entries with a tag
 */
class Magento_Cache_Frontend_TagDecorator implements Magento_Cache_FrontendInterface
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
     * @param Magento_Cache_FrontendInterface $type
     * @param string $tag Cache tag name
     */
    public function __construct(Magento_Cache_FrontendInterface $type, $tag)
    {
        $this->_frontend = $type;
        $this->_tag = $tag;
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
        if ($mode == Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG) {
            $result = false;
            foreach ($tags as $tag) {
                $result = $result
                    || $this->_frontend->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($tag, $this->_tag));
            }
        } else {
            if ($mode == Zend_Cache::CLEANING_MODE_ALL) {
                $mode = Zend_Cache::CLEANING_MODE_MATCHING_TAG;
            }
            $tags[] = $this->_tag;
            $result = $this->_frontend->clean($mode, $tags);
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return $this->_frontend->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($this->_tag));
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
