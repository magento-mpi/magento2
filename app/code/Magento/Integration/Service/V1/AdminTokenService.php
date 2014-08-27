<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service\V1;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Integration\Model\Oauth\Token as Token;
use Magento\Integration\Model\Oauth\Token\Factory as TokenModelFactory;
use Magento\Integration\Model\Resource\Oauth\Token\CollectionFactory as TokenCollectionFactory;
use Magento\User\Model\User as UserModel;

/**
 * Class to handle token generation for Admins
 *
 */
class AdminTokenService implements AdminTokenServiceInterface
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
     * Token Collection Factory
     *
     * @var TokenCollectionFactory
     */
    public $tokenModelCollectionFactory;

    /**
     * Initialize service
     *
     * @param TokenModelFactory $tokenModelFactory
     * @param UserModel $userModel
     * @param TokenCollectionFactory $tokenModelCollectionFactory
     */
    public function __construct(
        TokenModelFactory $tokenModelFactory,
        UserModel $userModel,
        TokenCollectionFactory $tokenModelCollectionFactory
    ) {
        $this->tokenModelFactory = $tokenModelFactory;
        $this->userModel = $userModel;
        $this->tokenModelCollectionFactory = $tokenModelCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createAdminAccessToken($username, $password)
    {
        $this->validateCredentials($username, $password);
        try {
            $this->userModel->login($username, $password);
            if (!$this->userModel->getId()) {
                /*
                 * This message is same as one thrown in \Magento\Backend\Model\Auth to keep the behavior consistent.
                 * Constant cannot be created in Auth Model since it uses legacy translation that doesn't support it.
                 * Need to make sure that this is refactored once exception handling is updated in Auth Model.
                 */
                throw new AuthenticationException('Please correct the user name or password.');
            }
        } catch (\Magento\Backend\Model\Auth\Exception $e) {
            throw new AuthenticationException($e->getMessage(), [], $e);
        } catch (\Magento\Framework\Model\Exception $e) {
            throw new LocalizedException($e->getMessage(), [], $e);
        }
        return $this->tokenModelFactory->create()->createAdminToken($this->userModel->getId())->getToken();
    }

    /**
     * Revoke token by admin id.
     *
     * @param int $adminId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function revokeAdminAccessToken($adminId)
    {
        $tokenCollection = $this->tokenModelCollectionFactory->create()->addFilterByAdminId($adminId);
        if ($tokenCollection->getSize() == 0) {
            throw new LocalizedException("This user has no tokens.");
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
