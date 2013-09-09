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
class Magento_FullPageCache_Model_Container_Accountlinks extends Magento_FullPageCache_Model_Container_Customer
{
    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager_Proxy
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager_Proxy $eventManager
     * @param Magento_FullPageCache_Model_Cache $fpcCache
     * @param Magento_FullPageCache_Model_Container_Placeholder $placeholder
     */
    public function __construct(
        Magento_Core_Model_Event_Manager_Proxy $eventManager,
        Magento_FullPageCache_Model_Cache $fpcCache,
        Magento_FullPageCache_Model_Container_Placeholder $placeholder
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($fpcCache, $placeholder);
    }

    /**
     * Get identifier from cookies
     *
     * @return string
     */
    protected function _getIdentifier()
    {
        $cacheId = $this->_getCookieValue(Magento_FullPageCache_Model_Cookie::COOKIE_CUSTOMER, '')
            . '_'
            . $this->_getCookieValue(Magento_FullPageCache_Model_Cookie::COOKIE_CUSTOMER_LOGGED_IN, '');
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
        /** @var $block Magento_Page_Block_Template_Links */
        $block = $this->_getPlaceHolderBlock();
        $block->setNameInLayout($this->_placeholder->getAttribute('name'));

        if (!$this->_getCookieValue(Magento_FullPageCache_Model_Cookie::COOKIE_CUSTOMER)
            || $this->_getCookieValue(Magento_FullPageCache_Model_Cookie::COOKIE_CUSTOMER_LOGGED_IN)
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
        } else {
            $this->_eventManager->dispatch('render_block_accountlinks', array(
                'block' => $block,
                'placeholder' => $this->_placeholder,
            ));
        }
        $this->_eventManager->dispatch('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));

        return $block->toHtml();
    }
}
