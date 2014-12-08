<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Controller\Ajax;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Zend\Http\Response;

/**
 * Login controller
 *
 * @method \Zend_Controller_Request_Http getRequest()
 * @method \Magento\Framework\App\Response\Http getResponse()
 */
class Login extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $session;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var \Magento\Core\Helper\Data $helper
     */
    protected $helper;

    /**
     * Initialize Login Service
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Core\Helper\Data $helper
     * @param AccountManagementInterface $accountManagement
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Core\Helper\Data $helper,
        AccountManagementInterface $accountManagement
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->accountManagement = $accountManagement;
    }

    /**
     * Login registered users and initiate a session.
     *
     * Expects a POST. ex for JSON {"username":"user@magento.com", "password":"userpassword"}
     *
     * @return void
     */
    public function execute()
    {
        $credentials = null;
        try {
            $credentials = $this->helper->jsonDecode($this->getRequest()->getRawBody());
        } catch (\Exception $e) {
            $this->getResponse()->setHttpResponseCode(Response::STATUS_CODE_400);
            return;
        }
        if (!$credentials || $this->getRequest()->getMethod() !== \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST) {
            $this->getResponse()->setHttpResponseCode(Response::STATUS_CODE_400);
            return;
        }
        $responseText = null;
        try {
            $customer = $this->accountManagement->authenticate($credentials['username'], $credentials['password']);
            $this->customerSession->setCustomerDataAsLoggedIn($customer);
            $this->customerSession->regenerateId();
        } catch (EmailNotConfirmedException $e) {
            $responseText = $e->getMessage();
        } catch (InvalidEmailOrPasswordException $e) {
            $responseText = $e->getMessage();
        } catch (\Exception $e) {
            $responseText = __('There was an error validating the username and password.');
        }
        if ($responseText) {
            $this->getResponse()->setHttpResponseCode(Response::STATUS_CODE_401);
        } else {
            $responseText = __('Login successful.');
        }
        $this->getResponse()->representJson($this->helper->jsonEncode(['message' => $responseText]));
    }
}
