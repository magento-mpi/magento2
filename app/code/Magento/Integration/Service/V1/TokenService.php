<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service\V1;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface as CustomerAccountService;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Integration\Model\Oauth\Token\Factory as TokenModelFactory;
use \Magento\User\Model\User as UserModel;

/**
 * Class to handle token generation for Admins and Customers
 */
class TokenService implements TokenServiceInterface
{
    /**
     * Token Model
     *
     * @var TokenModelFactory
     */
    private $tokenModelFactory;

    /**
     * User Model
     *
     * @var UserModel
     */
    private $userModel;

    /**
     * Customer Account Service
     *
     * @var CustomerAccountService
     */
    private $customerAccountService;

    /**
     * Initialize service
     *
     * @param TokenModelFactory $tokenModelFactory
     * @param UserModel $userModel
     * @param CustomerAccountService $customerAccountService
     */
    public function __construct(
        TokenModelFactory $tokenModelFactory,
        UserModel $userModel,
        CustomerAccountService $customerAccountService
    ) {
        $this->tokenModelFactory = $tokenModelFactory;
        $this->userModel = $userModel;
        $this->customerAccountService = $customerAccountService;
    }

    /**
     * {@inheritdoc}
     */
    public function createAdminAccessToken($username, $password)
    {
        $this->validateCredentials($username, $password);
        try {
            $this->userModel->login($username, $password);
        } catch (\Magento\Backend\Model\Auth\Exception $e) {
            throw new AuthenticationException($e->getMessage(), [], $e);
        } catch (\Magento\Framework\Model\Exception $e) {
            throw new LocalizedException($e->getMessage(), [], $e);
        }
        return $this->tokenModelFactory->create()->createAdminToken($this->userModel->getId())->getToken();
    }

    /**
     * {@inheritdoc}
     */
    public function createCustomerAccessToken($username, $password)
    {
        $this->validateCredentials($username, $password);
        $customerDataObject = $this->customerAccountService->authenticate($username, $password);
        return $this->tokenModelFactory->create()->createCustomerToken($customerDataObject->getId())->getToken();
    }

    /**
     * Validate user credentials
     *
     * @param string $userName
     * @param string $password
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function validateCredentials($userName, $password)
    {
        if (!$userName) {
            throw InputException::requiredField('userName');
        }
        if (!$password) {
            throw InputException::requiredField('password');
        }
    }
}
