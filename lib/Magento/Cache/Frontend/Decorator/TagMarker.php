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
class Magento_Cache_Frontend_Decorator_TagMarker extends Magento_Cache_Frontend_Decorator_Bare
{
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
        parent::__construct($frontend);
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
     * Enforce marking with a tag
     *
     * {@inheritdoc}
     */
    public function save($data, $id, array $tags = array(), $lifeTime = null)
    {
        $tags[] = $this->_tag;
        return parent::save($data, $id, $tags, $lifeTime);
    }
}
