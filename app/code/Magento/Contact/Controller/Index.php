<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Contact\Controller;

use Magento\Framework\App\Action\NotFoundException;
use Magento\Framework\App\RequestInterface;

/**
 * Contact index controller
 */
class Index extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_EMAIL_RECIPIENT = 'contact/email/recipient_email';

    const XML_PATH_EMAIL_SENDER = 'contact/email/sender_email_identity';

    const XML_PATH_EMAIL_TEMPLATE = 'contact/email/email_template';

    const XML_PATH_ENABLED = 'contact/contact/enabled';

    /**
     * @var \Magento\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Translate\Inline\StateInterface $inlineTranslation
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Translate\Inline\StateInterface $inlineTranslation
    ) {
        parent::__construct($context);
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\App\Action\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get(
            'Magento\Framework\App\Config\ScopeConfigInterface'
        )->isSetFlag(
            self::XML_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
        ) {
            throw new NotFoundException();
        }
        return parent::dispatch($request);
    }

    /**
     * Show Contact Us page
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock(
            'contactForm'
        )->setFormAction(
            $this->_objectManager->create('Magento\UrlInterface')->getUrl('*/*/post')
        );

        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

    /**
     * Post user question
     *
     * @return void
     * @throws \Exception
     */
    public function postAction()
    {
        if (!$this->getRequest()->isSecure()) {
            $this->_redirect('*/*/');
            return;
        }
        $post = $this->getRequest()->getPost();
        if ($post) {
            $this->inlineTranslation->suspend();
            try {
                $postObject = new \Magento\Object();
                $postObject->setData($post);

                $error = false;

                if (!\Zend_Validate::is(trim($post['name']), 'NotEmpty')) {
                    $error = true;
                }

                if (!\Zend_Validate::is(trim($post['comment']), 'NotEmpty')) {
                    $error = true;
                }

                if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if (\Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new \Exception();
                }

                $scopeConfig = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
                $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
                $transport = $this->_transportBuilder->setTemplateIdentifier(
                    $scopeConfig->getValue(
                        self::XML_PATH_EMAIL_TEMPLATE,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    )
                )->setTemplateOptions(
                    array(
                        'area' => \Magento\Core\Model\App\Area::AREA_FRONTEND,
                        'store' => $storeManager->getStore()->getId()
                    )
                )->setTemplateVars(
                    array('data' => $postObject)
                )->setFrom(
                    $scopeConfig->getValue(
                        self::XML_PATH_EMAIL_SENDER,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    )
                )->addTo(
                    $scopeConfig->getValue(
                        self::XML_PATH_EMAIL_RECIPIENT,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    )
                )->setReplyTo(
                    $post['email']
                )->getTransport();

                $transport->sendMessage();

                $this->inlineTranslation->resume();

                $this->messageManager->addSuccess(
                    __('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.')
                );
                $this->_redirect('*/*/');

                return;
            } catch (\Exception $e) {
                $this->inlineTranslation->resume();
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
                $this->_redirect('*/*/');
                return;
            }
        } else {
            $this->_redirect('*/*/');
        }
    }
}
