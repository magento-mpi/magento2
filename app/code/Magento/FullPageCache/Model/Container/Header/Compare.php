<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\FullPageCache\Model\Container\Header;

/**
 * Compare Link container
 */
class Compare extends \Magento\FullPageCache\Model\Container\AbstractContainer
{
    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        $identifier = $this->_placeholder->getAttribute('cache_id')
            . $this->_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_COMPARE_LIST, '');
        return 'CONTAINER_HEADER_COMPARE' . '_' . md5($identifier);
    }

    /**
     * Load cached data by cache id
     *
     * @param string $id
     * @return string|bool
     */
    protected function _loadCache($id)
    {
        if ($this->isRefreshRequired()) {
            return false;
        }
        return parent::_loadCache($id);
    }

    /**
     * Check if cache refresh is required
     *
     * Always return true in order to force refresh.
     *
     * @return bool
     */
    protected function isRefreshRequired()
    {
        return true;
    }

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        return $this->_getPlaceHolderBlock()->toHtml();
    }
}
