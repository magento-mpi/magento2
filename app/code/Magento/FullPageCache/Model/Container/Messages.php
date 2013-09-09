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
class Magento_FullPageCache_Model_Container_Messages extends Magento_FullPageCache_Model_Container_Abstract
{
    /**
     * Check for new messages. New message flag will be reseted if needed.
     *
     * @return bool
     */
    protected function _isNewMessageRecived()
    {
        return $this->_getCookieValue(Magento_FullPageCache_Model_Cookie::COOKIE_MESSAGE)
            || array_key_exists(Magento_FullPageCache_Model_Cache::REQUEST_MESSAGE_GET_PARAM, $_GET);
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
        Mage::getSingleton('Magento_Core_Model_Cookie')->delete(Magento_FullPageCache_Model_Cookie::COOKIE_MESSAGE);

        $block = $this->_getPlaceHolderBlock();

        $types = unserialize($this->_placeholder->getAttribute('storage_types'));
        foreach ($types as $type) {
            $this->_addMessagesToBlock($type, $block);
        }
        $this->_eventManager->dispatch('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));

        return $block->toHtml();
    }

    /**
     * Add messages from storage to message block
     *
     * @param string $messagesStorage
     * @param Magento_Core_Block_Messages $block
     */
    protected function _addMessagesToBlock($messagesStorage, Magento_Core_Block_Messages $block)
    {
        if ($storage = Mage::getSingleton($messagesStorage)) {
            $block->addMessages($storage->getMessages(true));
            $block->setEscapeMessageFlag($storage->getEscapeMessages(true));
        }
    }
}
