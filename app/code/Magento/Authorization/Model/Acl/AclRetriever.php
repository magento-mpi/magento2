<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authorization\Model\Acl;

use Magento\Authz\Model\UserIdentifier;
use Magento\Authz\Service\AuthorizationV1 as AuthorizationService;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\LocalizedException;

class AclRetriever extends AuthorizationService
{
    /**
     * Get a list of available resources using user
     *
     * @param UserIdentifier $userIdentifier
     * @return string[]
     * @throws AuthorizationException
     * @throws LocalizedException
     */
    public function getAllowedResourcesByUser($userIdentifier)
    {
        if ($userIdentifier->getUserType() == UserIdentifier::USER_TYPE_GUEST) {
            return [self::PERMISSION_ANONYMOUS];
        } elseif ($userIdentifier->getUserType() == UserIdentifier::USER_TYPE_CUSTOMER) {
            return [self::PERMISSION_SELF];
        }
        try {
            $role = $this->_getUserRole($userIdentifier);
            if (!$role) {
                throw new AuthorizationException('The role associated with the specified user cannot be found.');
            }
            $allowedResources = $this->getAllowedResourcesByRole($role->getId());
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            throw new LocalizedException(
                'Error happened while getting a list of allowed resources. Check exception log for details.'
            );
        }
        return $allowedResources;
    }

    /**
     * Get a list of available resource using user role id
     *
     * @param $roleId
     * @return string[]
     */
    public function getAllowedResourcesByRole($roleId)
    {
        $allowedResources = [];
        $rulesCollection = $this->_rulesCollectionFactory->create();
        $rulesCollection->getByRoles($roleId)->load();
        $acl = $this->_aclBuilder->getAcl();
        /** @var \Magento\Authorization\Model\Rules $ruleItem */
        foreach ($rulesCollection->getItems() as $ruleItem) {
            $resourceId = $ruleItem->getResourceId();
            if ($acl->has($resourceId) && $acl->isAllowed($roleId, $resourceId)) {
                $allowedResources[] = $resourceId;
            }
        }
        return $allowedResources;
    }
}
