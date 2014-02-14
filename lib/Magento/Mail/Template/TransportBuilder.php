<?php
/**
 * Mail Template Transport Builder
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Mail\Template;

class TransportBuilder
{
    /**
     * Template Identifier
     *
     * @var string
     */
    protected $templateIdentifier;

    /**
     * Template Variables
     *
     * @var array
     */
    protected $templateVars;

    /**
     * Template Options
     *
     * @var array
     */
    protected $templateOptions;

    /**
     * Mail Transport
     *
     * @var \Magento\Mail\TransportInterface
     */
    protected $transport;

    /**
     * Template Factory
     *
     * @var FactoryInterface
     */
    protected $templateFactory;

    /**
     * Object Manager
     *
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * Message
     *
     * @var \Magento\Mail\Message
     */
    protected $message;

    /**
     * Sender resolver
     *
     * @var \Magento\Mail\Template\SenderResolverInterface
     */
    protected $_senderResolver;

    /**
     * @param FactoryInterface $templateFactory
     * @param \Magento\Mail\Message $message
     * @param \Magento\Mail\Template\SenderResolverInterface $senderResolver
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\Mail\Template\FactoryInterface $templateFactory,
        \Magento\Mail\Message $message,
        \Magento\Mail\Template\SenderResolverInterface $senderResolver,
        \Magento\ObjectManager $objectManager
    ) {
        $this->templateFactory = $templateFactory;
        $this->message = $message;
        $this->objectManager = $objectManager;
        $this->_senderResolver = $senderResolver;
    }

    /**
     * Add cc address
     *
     * @param array|string $address
     * @param string $name
     * @return $this
     */
    public function addCc($address, $name = '')
    {
        $this->message->addCc($address, $name);
        return $this;
    }

    /**
     * Add to address
     *
     * @param array|string $address
     * @param string $name
     * @return $this
     */
    public function addTo($address, $name = '')
    {
        $this->message->addTo($address, $name);
        return $this;
    }

    /**
     * Add bcc address
     *
     * @param array|string $address
     * @return $this
     */
    public function addBcc($address)
    {
        $this->message->addBcc($address);
        return $this;
    }

    /**
     * Add bcc address
     *
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setReplyTo($email, $name = null)
    {
        $this->message->setReplyTo($email, $name);
        return $this;
    }

    /**
     * Set mail from address
     *
     * @param string $from
     * @return $this
     */
    public function setFrom($from)
    {
        $result = $this->_senderResolver->resolve($from);
        $this->message->setFrom($result['email'], $result['name']);
        return $this;
    }

    /**
     * Set template identifier
     *
     * @param string $templateIdentifier
     * @return $this
     */
    public function setTemplateIdentifier($templateIdentifier)
    {
        $this->templateIdentifier = $templateIdentifier;
        return $this;
    }

    /**
     * Set template vars
     *
     * @param array $templateVars
     * @return $this
     */
    public function setTemplateVars($templateVars)
    {
        $this->templateVars = $templateVars;
        return $this;
    }

    /**
     * Set template options
     *
     * @param array $templateOptions
     * @return $this
     */
    public function setTemplateOptions($templateOptions)
    {
        $this->templateOptions = $templateOptions;
        return $this;
    }

    /**
     * Get mail transport
     *
     * @return \Magento\Mail\TransportInterface
     */
    public function getTransport()
    {
        $this->prepareMessage();

        $result = $this->objectManager->create('Magento\Mail\TransportInterface', array(
            'message' => clone $this->message
        ));

        $this->reset();

        return $result;
    }

    /**
     * Reset object state
     *
     * @return $this
     */
    protected function reset()
    {
        $this->message = $this->objectManager->create('\Magento\Mail\Message');
        $this->templateIdentifier = null;
        $this->templateVars = null;
        $this->templateOptions = null;
        return $this;
    }

    /**
     * Get template
     *
     * @return \Magento\Mail\TemplateInterface
     */
    protected function getTemplate()
    {
        return $this->templateFactory->get($this->templateIdentifier)
            ->setVars($this->templateVars)
            ->setOptions($this->templateOptions);
    }

    /**
     * Prepare message
     *
     * @return $this
     */
    protected function prepareMessage()
    {
        $template = $this->getTemplate();
        $types = array(
            \Magento\App\TemplateTypesInterface::TYPE_TEXT => \Magento\Mail\MessageInterface::TYPE_TEXT,
            \Magento\App\TemplateTypesInterface::TYPE_HTML => \Magento\Mail\MessageInterface::TYPE_HTML,
        );

        $this->message->setBody($template->processTemplate())
            ->setMessageType($types[$template->getType()])
            ->setSubject($template->getSubject());

        return $this;
    }
}
