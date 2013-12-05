<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Message;

/**
 * Message manager model
 */
class Manager
{
    /**
     * Default message group
     */
    const DEFAULT_GROUP = 'default';

    /**
     * Configuration path to log exception file
     */
    const XML_PATH_LOG_EXCEPTION_FILE = 'dev/log/exception_file';

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Factory
     */
    protected $messageFactory;

    /**
     * @var CollectionFactory
     */
    protected $messagesFactory;

    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * File for exceptions log
     *
     * @var string
     */
    protected $exceptionLogFile;

    /**
     * @param Session $session
     * @param Factory $messageFactory
     * @param CollectionFactory $messagesFactory
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Logger $logger
     * @param $exceptionLogFile
     */
    public function __construct(
        Session $session,
        Factory $messageFactory,
        CollectionFactory $messagesFactory,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Logger $logger,
        $exceptionLogFile
    ) {
        $this->session = $session;
        $this->messageFactory = $messageFactory;
        $this->messagesFactory = $messagesFactory;
        $this->eventManager = $eventManager;
        $this->logger = $logger;
        $this->exceptionLogFile = $exceptionLogFile;
    }

    /**
     * Retrieve messages
     *
     * @param string $group
     * @param bool $clear
     * @return Collection
     */
    public function getMessages($group = self::DEFAULT_GROUP, $clear = false)
    {
        if (!$this->session->getData($group)) {
            $this->session->setData($group, $this->messagesFactory->create());
        }

        if ($clear) {
            $messages = clone $this->session->getData($group);
            $this->session->getData($group)->clear();
            $this->eventManager->dispatch('core_session_abstract_clear_messages');
            return $messages;
        }
        return $this->session->getData($group);
    }

    /**
     * Adding new message to message collection
     *
     * @param MessageInterface $message
     * @param string $group
     * @return $this
     */
    public function addMessage(MessageInterface $message, $group = self::DEFAULT_GROUP)
    {
        $this->getMessages($group)->addMessage($message);
        $this->eventManager->dispatch('core_session_abstract_add_message');
        return $this;
    }

    /**
     * Adding messages array to message collection
     *
     * @param array $messages
     * @param string $group
     * @return $this
     */
    public function addMessages(array $messages, $group = self::DEFAULT_GROUP)
    {
        if (is_array($messages)) {
            foreach ($messages as $message) {
                $this->addMessage($message, $group);
            }
        }
        return $this;
    }

    /**
     * Adding new error message
     *
     * @param string $message
     * @param string $group
     * @return $this
     */
    public function addError($message, $group = self::DEFAULT_GROUP)
    {
        $this->addMessage($this->messageFactory->create(MessageInterface::TYPE_ERROR, $message), $group);
        return $this;
    }

    /**
     * Adding new warning message
     *
     * @param string $message
     * @param string $group
     * @return $this
     */
    public function addWarning($message, $group = self::DEFAULT_GROUP)
    {
        $this->addMessage($this->messageFactory->create(MessageInterface::TYPE_WARNING, $message), $group);
        return $this;
    }

    /**
     * Adding new notice message
     *
     * @param string $message
     * @param string $group
     * @return $this
     */
    public function addNotice($message, $group = self::DEFAULT_GROUP)
    {
        $this->addMessage($this->messageFactory->create(MessageInterface::TYPE_NOTICE, $message), $group);
        return $this;
    }

    /**
     * Adding new success message
     *
     * @param string $message
     * @param string $group
     * @return $this
     */
    public function addSuccess($message, $group = self::DEFAULT_GROUP)
    {
        $this->addMessage($this->messageFactory->create(MessageInterface::TYPE_SUCCESS, $message), $group);
        return $this;
    }

    /**
     * Adds messages array to message collection, but doesn't add duplicates to it
     *
     * @param array|MessageInterface $messages
     * @param string $group
     * @return $this
     */
    public function addUniqueMessages($messages, $group = self::DEFAULT_GROUP)
    {
        if (!is_array($messages)) {
            $messages = array($messages);
        }
        if (empty($messages)) {
            return $this;
        }

        $messagesAlready = array();
        $items = $this->getMessages($group)->getItems();
        foreach ($items as $item) {
            if ($item instanceof MessageInterface) {
                $text = $item->getText();
                $messagesAlready[$text] = true;
            }
        }

        foreach ($messages as $message) {
            if ($message instanceof MessageInterface) {
                $text = $message->getText();
            } else {
                continue; // Some unknown object, add it anyway
            }

            // Check for duplication
            if (isset($messagesAlready[$text])) {
                continue;
            }
            $messagesAlready[$text] = true;
            $this->addMessage($message, $group);
        }

        return $this;
    }

    /**
     * Not Magento exception handling
     *
     * @param \Exception $exception
     * @param string $alternativeText
     * @param string $group
     * @return $this
     */
    public function addException(\Exception $exception, $alternativeText, $group = self::DEFAULT_GROUP)
    {
        $message = sprintf(
            'Exception message: %s%sTrace: %s',
            $exception->getMessage(),
            "\n",
            $exception->getTraceAsString()
        );

        $this->logger->logFile($message, \Zend_Log::DEBUG, $this->exceptionLogFile);
        $this->addMessage($this->messageFactory->create(MessageInterface::TYPE_ERROR, $alternativeText), $group);
        return $this;
    }
}
