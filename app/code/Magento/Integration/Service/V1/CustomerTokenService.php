<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service\V1;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface as CustomerAccountService;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Integration\Model\Oauth\Token\Factory as TokenModelFactory;
use Magento\Integration\Model\Oauth\Token as Token;
use Magento\Integration\Model\Resource\Oauth\Token\CollectionFactory as TokenCollectionFactory;

class CustomerTokenService implements CustomerTokenServiceInterface
{
    /**
     * Token Model
     *
     * @var TokenModelFactory
     */
    private $tokenModelFactory;

    /**
     * Customer Account Service
     *
     * @var CustomerAccountService
     */
    private $customerAccountService;

    /**
     * Token Collection Factory
     *
     * @var TokenCollectionFactory
     */
    public $tokenModelCollectionFactory;

    /**
     * Initialize service
     *
     * @param TokenModelFactory $tokenModelFactory
     * @param CustomerAccountService $customerAccountService
     * @param TokenCollectionFactory $tokenModelCollectionFactory
     */
    public function __construct(
        TokenModelFactory $tokenModelFactory,
        CustomerAccountService $customerAccountService,
        TokenCollectionFactory $tokenModelCollectionFactory
    ) {
        $this->tokenModelFactory = $tokenModelFactory;
        $this->customerAccountService = $customerAccountService;
        $this->tokenModelCollectionFactory = $tokenModelCollectionFactory;
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
     * Revoke token by customer id.
     *
     * @param int $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function revokeCustomerAccessToken($customerId)
    {
        $tokenCollection = $this->tokenModelCollectionFactory->create()->addFilterByCustomerId($customerId);
        if ($tokenCollection->getSize() == 0) {
            throw new LocalizedException("This customer has no tokens.");
        }
        try {
            foreach ($tokenCollection as $token) {
                $token->setRevoked(1)->save();
            }
        } catch (\Exception $e) {
            throw new LocalizedException("The tokens could not be revoked.");
        }
        return true;
    }

    /**
     * Validate user credentials
     *
     * @param string $username
     * @param string $password
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function validateCredentials($username, $password)
    {
        $exception = new InputException();
        if (!is_string($username) || strlen($username) == 0) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'username']);
        }
        if (!is_string($username) || strlen($password) == 0) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'password']);
        }
        if ($exception->wasErrorAdded()) {
            throw $exception;
        }
    }
}