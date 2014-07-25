<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Service\V1\AuthorizationServiceTest;

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
