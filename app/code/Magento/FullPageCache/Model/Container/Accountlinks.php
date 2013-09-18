<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Account links container
 */
namespace Magento\FullPageCache\Model\Container;

class Accountlinks extends \Magento\FullPageCache\Model\Container\Customer
{
    /**
     * Get identifier from cookies
     *
     * @return string
     */
    protected function _getIdentifier()
    {
        $cacheId = $this->_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER, '')
            . '_'
            . $this->_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER_LOGGED_IN, '');
        return $cacheId;
    }

    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        return 'CONTAINER_LINKS_' . md5($this->_placeholder->getAttribute('cache_id') . $this->_getIdentifier());
    }

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        /** @var $block \Magento\Page\Block\Template\Links */
        $block = $this->_getPlaceHolderBlock();
        $block->setNameInLayout($this->_placeholder->getAttribute('name'));

        if (!$this->_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER)
            || $this->_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER_LOGGED_IN)
        ) {
            $links = $this->_placeholder->getAttribute('links');
            if ($links) {
                $links = unserialize(base64_decode($links));
                foreach ($links as $position => $linkInfo) {
                    $block->addLink($linkInfo['label'], $linkInfo['url'], $linkInfo['title'], true, array(), $position,
                        $linkInfo['li_params'], $linkInfo['a_params'], $linkInfo['before_text'], $linkInfo['after_text']
                    );
                }
            }
        }
        $this->_eventManager->dispatch('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));

        return $block->toHtml();
    }
}
