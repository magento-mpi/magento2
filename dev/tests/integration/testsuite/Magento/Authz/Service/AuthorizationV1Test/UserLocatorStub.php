<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authz\Service\AuthorizationV1Test;

use Magento\Authz\Model\UserLocatorInterface;

class UserLocatorStub implements UserLocatorInterface
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
