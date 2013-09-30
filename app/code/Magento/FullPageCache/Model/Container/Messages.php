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
     * @var Magento_Core_Model_Cookie
     */
    protected $_coreCookie;

    /**
     * @var Magento_FullPageCache_Model_Container_MessagesStorageFactory
     */
    protected $_storageFactory;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_FullPageCache_Model_Cache $fpcCache
     * @param Magento_FullPageCache_Model_Container_Placeholder $placeholder
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_FullPageCache_Helper_Url $urlHelper
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Layout $layout
     * @param Magento_Core_Model_Cookie $coreCookie
     * @param Magento_FullPageCache_Model_Container_MessagesStorageFactory $storageFactory
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_FullPageCache_Model_Cache $fpcCache,
        Magento_FullPageCache_Model_Container_Placeholder $placeholder,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_FullPageCache_Helper_Url $urlHelper,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Layout $layout,
        Magento_Core_Model_Cookie $coreCookie,
        Magento_FullPageCache_Model_Container_MessagesStorageFactory $storageFactory
    ) {
        parent::__construct(
            $eventManager, $fpcCache, $placeholder, $coreRegistry, $urlHelper, $coreStoreConfig, $layout
        );

        $this->_coreCookie = $coreCookie;
        $this->_storageFactory = $storageFactory;
    }

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
        $this->_coreCookie->delete(Magento_FullPageCache_Model_Cookie::COOKIE_MESSAGE);

        $block = $this->_getPlaceHolderBlock();

        // TODO: Getting of storage class name needs to be located in messages storage factory
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
        if ($storage = $this->_storageFactory->get($messagesStorage)) {
            $block->addMessages($storage->getMessages(true));
            $block->setEscapeMessageFlag($storage->getEscapeMessages(true));
        }
    }
}
