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
 * Cart sidebar container
 */
namespace Magento\FullPageCache\Model\Container;

class Messages extends \Magento\FullPageCache\Model\Container\AbstractContainer
{
    /**
     * Check for new messages. New message flag will be reseted if needed.
     *
     * @return bool
     */
    protected function _isNewMessageRecived()
    {
        return $this->_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_MESSAGE)
            || array_key_exists(\Magento\FullPageCache\Model\Cache::REQUEST_MESSAGE_GET_PARAM, $_GET);
    }

    /**
     * Redirect to content processing on new message
     *
     * @param string $content
     * @return bool
     */
    public function applyWithoutApp(&$content)
    {
        if ($this->_isNewMessageRecived()) {
            return false;
        }
        return parent::applyWithoutApp($content);
    }

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        \Mage::getSingleton('Magento\Core\Model\Cookie')->delete(\Magento\FullPageCache\Model\Cookie::COOKIE_MESSAGE);

        $block = $this->_getPlaceHolderBlock();

        $types = unserialize($this->_placeholder->getAttribute('storage_types'));
        foreach ($types as $type) {
            $this->_addMessagesToBlock($type, $block);
        }
        \Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));

        return $block->toHtml();
    }

    /**
     * Add messages from storage to message block
     *
     * @param string $messagesStorage
     * @param \Magento\Core\Block\Messages $block
     */
    protected function _addMessagesToBlock($messagesStorage, \Magento\Core\Block\Messages $block)
    {
        if ($storage = \Mage::getSingleton($messagesStorage)) {
            $block->addMessages($storage->getMessages(true));
            $block->setEscapeMessageFlag($storage->getEscapeMessages(true));
        }
    }
}
