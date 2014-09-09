<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service\V1;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface as CustomerAccountService;
use Magento\Framework\Exception\LocalizedException;
use Magento\Integration\Helper\Validator;
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
     * @var \Magento\Integration\Helper\Validator
     */
    private $validatorHelper;

    /**
     * Token Collection Factory
     *
     * @var TokenCollectionFactory
     */
    private $tokenModelCollectionFactory;

    /**
     * Initialize service
     *
     * @param TokenModelFactory $tokenModelFactory
     * @param CustomerAccountService $customerAccountService
     * @param TokenCollectionFactory $tokenModelCollectionFactory
     * @param \Magento\Integration\Helper\Validator $validatorHelper
     */
    public function __construct(
        TokenModelFactory $tokenModelFactory,
        CustomerAccountService $customerAccountService,
        TokenCollectionFactory $tokenModelCollectionFactory,
        Validator $validatorHelper
    ) {
        $this->tokenModelFactory = $tokenModelFactory;
        $this->customerAccountService = $customerAccountService;
        $this->tokenModelCollectionFactory = $tokenModelCollectionFactory;
        $this->validatorHelper = $validatorHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function createCustomerAccessToken($username, $password)
    {
        $this->validatorHelper->validateCredentials($username, $password);
        $customerDataObject = $this->customerAccountService->authenticate($username, $password);
        return $this->tokenModelFactory->create()->createCustomerToken($customerDataObject->getId())->getToken();
    }

    /**
     * {@inheritdoc}
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
}
