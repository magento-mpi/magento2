<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Element;

/**
 * Class Messages
 */
class Messages extends Template
{
    /**
     * Messages collection
     *
     * @var \Magento\Message\Collection
     */
    protected $messages;

    /**
     * Store first level html tag name for messages html output
     *
     * @var string
     */
    protected $firstLevelTagName = 'ul';

    /**
     * Store second level html tag name for messages html output
     *
     * @var string
     */
    protected $secondLevelTagName = 'li';

    /**
     * Store content wrapper html tag name for messages html output
     *
     * @var string
     */
    protected $contentWrapTagName = 'span';

    /**
     * Storage for used types of message storages
     *
     * @var array
     */
    protected $usedStorageTypes = array();

    /**
     * Grouped message types
     *
     * @var string[]
     */
    protected $messageTypes = array(
        \Magento\Message\MessageInterface::TYPE_ERROR,
        \Magento\Message\MessageInterface::TYPE_WARNING,
        \Magento\Message\MessageInterface::TYPE_NOTICE,
        \Magento\Message\MessageInterface::TYPE_SUCCESS
    );

    /**
     * Message singleton
     *
     * @var \Magento\Message\Factory
     */
    protected $messageFactory;

    /**
     * Message model factory
     *
     * @var \Magento\Message\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Message manager
     *
     * @var \Magento\Message\ManagerInterface
     */
    protected $messageManager;
    
    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param \Magento\Message\Factory $messageFactory
     * @param \Magento\Message\CollectionFactory $collectionFactory
     * @param \Magento\Message\ManagerInterface $messageManager
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Message\Factory $messageFactory,
        \Magento\Message\CollectionFactory $collectionFactory,
        \Magento\Message\ManagerInterface $messageManager,
        array $data = array()
    ) {
        $this->messageFactory = $messageFactory;
        $this->collectionFactory = $collectionFactory;
        $this->messageManager = $messageManager;
        parent::__construct($context, $data);
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addStorageType($this->messageManager->getDefaultGroup());
        $this->addMessages($this->messageManager->getMessages(true));
        parent::_prepareLayout();
        return $this;
    }

    /**
     * Set messages collection
     *
     * @param   \Magento\Message\Collection $messages
     * @return  $this
     */
    public function setMessages(\Magento\Message\Collection $messages)
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * Add messages to display
     *
     * @param \Magento\Message\Collection $messages
     * @return $this
     */
    public function addMessages(\Magento\Message\Collection $messages)
    {
        foreach ($messages->getItems() as $message) {
            $this->getMessageCollection()->addMessage($message);
        }
        return $this;
    }

    /**
     * Retrieve messages collection
     *
     * @return \Magento\Message\Collection
     */
    public function getMessageCollection()
    {
        if (!($this->messages instanceof \Magento\Message\Collection)) {
            $this->messages = $this->collectionFactory->create();
        }
        return $this->messages;
    }

    /**
     * Adding new message to message collection
     *
     * @param \Magento\Message\MessageInterface $message
     * @return $this
     */
    public function addMessage(\Magento\Message\AbstractMessage $message)
    {
        $this->getMessageCollection()->addMessage($message);
        return $this;
    }

    /**
     * Adding new error message
     *
     * @param   string $message
     * @return  $this
     */
    public function addError($message)
    {
        $this->addMessage($this->messageFactory->create(\Magento\Message\MessageInterface::TYPE_ERROR, $message));
        return $this;
    }

    /**
     * Adding new warning message
     *
     * @param   string $message
     * @return  $this
     */
    public function addWarning($message)
    {
        $this->addMessage($this->messageFactory->create(\Magento\Message\MessageInterface::TYPE_WARNING, $message));
        return $this;
    }

    /**
     * Adding new notice message
     *
     * @param   string $message
     * @return  $this
     */
    public function addNotice($message)
    {
        $this->addMessage($this->messageFactory->create(\Magento\Message\MessageInterface::TYPE_NOTICE, $message));
        return $this;
    }

    /**
     * Adding new success message
     *
     * @param   string $message
     * @return  $this
     */
    public function addSuccess($message)
    {
        $this->addMessage($this->messageFactory->create(\Magento\Message\MessageInterface::TYPE_SUCCESS, $message));
        return $this;
    }

    /**
     * Retrieve messages array by message type
     *
     * @param   string $type
     * @return  array
     */
    public function getMessagesByType($type)
    {
        return $this->getMessageCollection()->getItemsByType($type);
    }

    /**
     * Return grouped message types
     *
     * @return array
     */
    public function getMessageTypes()
    {
        return $this->messageTypes;
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
     * @param null|string|array|\Magento\Object &$html
     * @return void
     */
    protected function _dispatchRenderGroupedAfterEvent(&$html)
    {
        $transport = new \Magento\Object(array('output' => $html));
        $params = array(
            'element_name' => $this->getNameInLayout(),
            'layout'       => $this->getLayout(),
            'transport'    => $transport,
        );
        $this->_eventManager->dispatch('view_message_block_render_grouped_html_after', $params);
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
            if ($messages = $this->getMessagesByType($type)) {
                if (!$html) {
                    $html .= '<' . $this->firstLevelTagName . ' class="messages">';
                }
                $html .= '<' . $this->secondLevelTagName . ' class="' . $type . '-msg">';
                $html .= '<' . $this->firstLevelTagName . '>';

                foreach ($messages as $message) {
                    $html.= '<' . $this->secondLevelTagName . '>';
                    $html.= '<' . $this->contentWrapTagName .  $this->getUiId('message', $type) .  '>';
                    $html.= $message->getText();
                    $html.= '</' . $this->contentWrapTagName . '>';
                    $html.= '</' . $this->secondLevelTagName . '>';
                }
                $html .= '</' . $this->firstLevelTagName . '>';
                $html .= '</' . $this->secondLevelTagName . '>';
            }
        }
        if ($html) {
            $html .= '</' . $this->firstLevelTagName . '>';
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
     * @return void
     */
    public function setFirstLevelTagName($tagName)
    {
        $this->firstLevelTagName = $tagName;
    }

    /**
     * Set messages first level html tag name for output messages as html
     *
     * @param string $tagName
     * @return void
     */
    public function setSecondLevelTagName($tagName)
    {
        $this->secondLevelTagName = $tagName;
    }

    /**
     * Get cache key informative items
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
            'storage_types' => serialize($this->usedStorageTypes)
        );
    }

    /**
     * Add used storage type
     *
     * @param string $type
     * @return void
     */
    public function addStorageType($type)
    {
        $this->usedStorageTypes[] = $type;
    }
}
