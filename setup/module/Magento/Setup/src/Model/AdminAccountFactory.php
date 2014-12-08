<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Setup\Module\Setup;
use Zend\ServiceManager\ServiceLocatorInterface;

class AdminAccountFactory
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @param Setup $setup
     * @param array $data
     * @return AdminAccount
     */
    public function create(Setup $setup, $data)
    {
        return new AdminAccount(
            $setup,
            $this->serviceLocator->get('Magento\Framework\Math\Random'),
            $data
        );
    }
}
