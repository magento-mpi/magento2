<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Messages block
 *
 * @category   Magento
 * @package    Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Block_Messages extends Magento_Core_Block_Template
{
    /**
     * Messages collection
     *
     * @var Magento_Core_Model_Message_Collection
     */
    protected $_messages;

    /**
     * Store first level html tag name for messages html output
     *
     * @var string
     */
    protected $_messagesFirstLevelTagName = 'ul';

    /**
     * Store second level html tag name for messages html output
     *
     * @var string
     */
    protected $_messagesSecondLevelTagName = 'li';

    /**
     * Store content wrapper html tag name for messages html output
     *
     * @var string
     */
    protected $_messagesContentWrapperTagName = 'span';

    /**
     * Flag which require message text escape
     *
     * @var bool
     */
    protected $_escapeMessageFlag = false;

    /**
     * Storage for used types of message storages
     *
     * @var array
     */
    protected $_usedStorageTypes = array();

    /**
     * Grouped message types
     *
     * @var array
     */
    protected $_messageTypes = array(
        Magento_Core_Model_Message::ERROR,
        Magento_Core_Model_Message::WARNING,
        Magento_Core_Model_Message::NOTICE,
        Magento_Core_Model_Message::SUCCESS
    );

    /**
     * Preparing global layout
     *
     * @return Magento_Core_Block_Messages
     */
    protected function _prepareLayout()
    {
        $this->addStorageType(get_class($this->_session));
        $this->addMessages($this->_session->getMessages(true));
        parent::_prepareLayout();
        return $this;
    }

    /**
     * Set message escape flag
     *
     * @param bool $flag
     * @return Magento_Core_Block_Messages
     */
    public function setEscapeMessageFlag($flag)
    {
        $this->_escapeMessageFlag = $flag;
        return $this;
    }

    /**
     * Set messages collection
     *
     * @param   Magento_Core_Model_Message_Collection $messages
     * @return  Magento_Core_Block_Messages
     */
    public function setMessages(Magento_Core_Model_Message_Collection $messages)
    {
        $this->_messages = $messages;
        return $this;
    }

    /**
     * Add messages to display
     *
     * @param Magento_Core_Model_Message_Collection $messages
     * @return Magento_Core_Block_Messages
     */
    public function addMessages(Magento_Core_Model_Message_Collection $messages)
    {
        foreach ($messages->getItems() as $message) {
            $this->getMessageCollection()->add($message);
        }
        return $this;
    }

    /**
     * Retrieve messages collection
     *
     * @return Magento_Core_Model_Message_Collection
     */
    public function getMessageCollection()
    {
        if (!($this->_messages instanceof Magento_Core_Model_Message_Collection)) {
            $this->_messages = Mage::getModel('Magento_Core_Model_Message_Collection');
        }
        return $this->_messages;
    }

    /**
     * Adding new message to message collection
     *
     * @param   Magento_Core_Model_Message_Abstract $message
     * @return  Magento_Core_Block_Messages
     */
    public function addMessage(Magento_Core_Model_Message_Abstract $message)
    {
        $this->getMessageCollection()->add($message);
        return $this;
    }

    /**
     * Adding new error message
     *
     * @param   string $message
     * @return  Magento_Core_Block_Messages
     */
    public function addError($message)
    {
        $this->addMessage(Mage::getSingleton('Magento_Core_Model_Message')->error($message));
        return $this;
    }

    /**
     * Adding new warning message
     *
     * @param   string $message
     * @return  Magento_Core_Block_Messages
     */
    public function addWarning($message)
    {
        $this->addMessage(Mage::getSingleton('Magento_Core_Model_Message')->warning($message));
        return $this;
    }

    /**
     * Adding new notice message
     *
     * @param   string $message
     * @return  Magento_Core_Block_Messages
     */
    public function addNotice($message)
    {
        $this->addMessage(Mage::getSingleton('Magento_Core_Model_Message')->notice($message));
        return $this;
    }

    /**
     * Adding new success message
     *
     * @param   string $message
     * @return  Magento_Core_Block_Messages
     */
    public function addSuccess($message)
    {
        $this->addMessage(Mage::getSingleton('Magento_Core_Model_Message')->success($message));
        return $this;
    }

    /**
     * Retrieve messages array by message type
     *
     * @param   string $type
     * @return  array
     */
    public function getMessages($type=null)
    {
        return $this->getMessageCollection()->getItems($type);
    }

    /**
     * Retrieve messages in HTML format
     *
     * @param   string $type
     * @return  string
     */
    public function getHtml($type = null)
    {
        $html = '<' . $this->_messagesFirstLevelTagName . ' id="admin_messages">';
        foreach ($this->getMessages($type) as $message) {
            $html .= '<' . $this->_messagesSecondLevelTagName . ' class="' . $message->getType() . '-msg" '
                . $this->getUiId('message') . '>'
                . $this->_escapeMessageFlag ? $this->escapeHtml($message->getText()) : $message->getText()
                . '</' . $this->_messagesSecondLevelTagName . '>';
        }
        $html .= '</' . $this->_messagesFirstLevelTagName . '>';
        return $html;
    }

    /**
     * Return grouped message types
     *
     * @return array
     */
    public function getMessageTypes()
    {
        return $this->_messageTypes;
    }

    /**
     * Retrieve messages in HTML format grouped by type
     *
     * @return string
     */
    public function getGroupedHtml()
    {
        $html = $this->_renderMessagesByType();
        $this->_dispatchRenderGroupedAfterEvent($html);
        return $html;
    }

    /**
     * Dispatch render after event
     *
     * @param $html
     */
    protected function _dispatchRenderGroupedAfterEvent(&$html)
    {
        $transport = new Magento_Object(array('output' => $html));
        $params = array(
            'element_name' => $this->getNameInLayout(),
            'layout'       => $this->getLayout(),
            'transport'    => $transport,
        );
        $this->_eventManager->dispatch('core_message_block_render_grouped_html_after', $params);
        $html = $transport->getData('output');
    }

    /**
     * Render messages in HTML format grouped by type
     *
     * @return string
     */
    protected function _renderMessagesByType()
    {
        $html = '';
        foreach ($this->getMessageTypes() as $type) {
            if ($messages = $this->getMessages($type)) {
                if (!$html) {
                    $html .= '<' . $this->_messagesFirstLevelTagName . ' class="messages">';
                }
                $html .= '<' . $this->_messagesSecondLevelTagName . ' class="' . $type . '-msg">';
                $html .= '<' . $this->_messagesFirstLevelTagName . '>';

                foreach ($messages as $message) {
                    $html.= '<' . $this->_messagesSecondLevelTagName . '>';
                    $html.= '<' . $this->_messagesContentWrapperTagName .  $this->getUiId('message', $type) .  '>';
                    $html.= ($this->_escapeMessageFlag) ? $this->escapeHtml($message->getText()) : $message->getText();
                    $html.= '</' . $this->_messagesContentWrapperTagName . '>';
                    $html.= '</' . $this->_messagesSecondLevelTagName . '>';
                }
                $html .= '</' . $this->_messagesFirstLevelTagName . '>';
                $html .= '</' . $this->_messagesSecondLevelTagName . '>';
            }
        }
        if ($html) {
            $html .= '</' . $this->_messagesFirstLevelTagName . '>';
        }
        return $html;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getTemplate()) {
            $html = parent::_toHtml();
        } else {
            $html = $this->_renderMessagesByType();
        }
        return $html;
    }

    /**
     * Set messages first level html tag name for output messages as html
     *
     * @param string $tagName
     */
    public function setMessagesFirstLevelTagName($tagName)
    {
        $this->_messagesFirstLevelTagName = $tagName;
    }

    /**
     * Set messages first level html tag name for output messages as html
     *
     * @param string $tagName
     */
    public function setMessagesSecondLevelTagName($tagName)
    {
        $this->_messagesSecondLevelTagName = $tagName;
    }

    /**
     * Get cache key informative items
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
            'storage_types' => serialize($this->_usedStorageTypes)
        );
    }

    /**
     * Add used storage type
     *
     * @param string $type
     */
    public function addStorageType($type)
    {
        $this->_usedStorageTypes[] = $type;
    }

    /**
     * Whether or not to escape the message.
     *
     * @return boolean
     */
    public function shouldEscapeMessage()
    {
        return $this->_escapeMessageFlag;
    }
}
