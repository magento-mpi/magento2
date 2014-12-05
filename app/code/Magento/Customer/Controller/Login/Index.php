<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Controller\Login;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Webapi\Exception as HttpException;
use Magento\Webapi\Exception;

/**
 * Login controller
 */
class Index extends \Magento\Framework\App\Action\Action
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
     * Expects a POST. ex for JSON  {"username":"user@magento.com", "password":"userpassword"}
     */
    public function execute()
    {
        $login = null;
        try {
            $login = $this->helper->jsonDecode($this->getRequest()->getRawBody());
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode($e->getCode());
            return;
        }
        if (!$login || $this->getRequest()->getMethod() !== \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST) {
            $this->getResponse()->setHttpResponseCode(HttpException::HTTP_BAD_REQUEST);
            return;
        }
        $responseText = null;
        try {
            $customer = $this->accountManagement->authenticate($login['username'], $login['password']);
            $this->customerSession->setCustomerDataAsLoggedIn($customer);
            $this->customerSession->regenerateId();
        } catch (EmailNotConfirmedException $e) {
            $responseText = $e->getMessage();
        } catch (InvalidEmailOrPasswordException $e) {
            $responseText = $e->getMessage();
        } catch (\Exception $e) {
            $responseText = __('There was an error validating the login and password.');
        }
        if ($responseText) {
            $this->getResponse()->setHttpResponseCode(HttpException::HTTP_UNAUTHORIZED);
        } else {
            $responseText = __('Login successful.');
        }
        $this->getResponse()->representJson($this->helper->jsonEncode(['loginMessage' => $responseText]));
    }
}
