<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Contacts
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Contacts index controller
 *
 * @category   Magento
 * @package    Magento_Contacts
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Contacts\Controller;

use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;

class Index extends \Magento\App\Action\Action
{
    const XML_PATH_EMAIL_RECIPIENT  = 'contacts/email/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'contacts/email/email_template';
    const XML_PATH_ENABLED          = 'contacts/contacts/enabled';

    /**
     * @var \Magento\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Translate\Inline\StateInterface $inlineTranslation
     */
    public function __construct(
        \Magento\App\Action\Context $context,
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
     * @return \Magento\App\ResponseInterface
     * @throws \Magento\App\Action\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\Core\Model\Store\Config')->getConfigFlag(self::XML_PATH_ENABLED)) {
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
        $this->_view->getLayout()->getBlock('contactForm')
            ->setFormAction($this->_objectManager->create('Magento\UrlInterface')->getUrl('*/*/post'));

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

                $storeConfig = $this->_objectManager->get('Magento\Core\Model\Store\Config');
                $storeManager = $this->_objectManager->get('Magento\Core\Model\StoreManagerInterface');
                $transport = $this->_transportBuilder
                    ->setTemplateIdentifier($storeConfig->getConfig(self::XML_PATH_EMAIL_TEMPLATE))
                    ->setTemplateOptions(array(
                        'area' => \Magento\Core\Model\App\Area::AREA_FRONTEND,
                        'store' => $storeManager->getStore()->getId()
                    ))
                    ->setTemplateVars(array('data' => $postObject))
                    ->setFrom($storeConfig->getConfig(self::XML_PATH_EMAIL_SENDER))
                    ->addTo($storeConfig->getConfig(self::XML_PATH_EMAIL_RECIPIENT))
                    ->setReplyTo($post['email'])
                    ->getTransport();

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
