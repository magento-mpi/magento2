<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authz\Model\UserIdentifier;

use Magento\Framework\ObjectManager;
use Magento\Authz\Model\UserIdentifier;

/**
 * User identifier factory.
 */
class Factory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize dependencies
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create instance of user identifier.
     *
     * @param string $userType
     * @param int $userId
     * @return UserIdentifier
     */
    public function create($userType, $userId = 0)
    {
        return $this->_objectManager->create(
            'Magento\Authz\Model\UserIdentifier',
            array('userType' => $userType, 'userId' => $userId)
        );
    }
}
