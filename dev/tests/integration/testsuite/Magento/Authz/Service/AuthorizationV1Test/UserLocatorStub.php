<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authz\Service\AuthorizationV1Test;

use Magento\Authorization\Model\UserContextInterface;

class UserLocatorStub implements UserContextInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getUserType()
    {
        return '';
    }
}
