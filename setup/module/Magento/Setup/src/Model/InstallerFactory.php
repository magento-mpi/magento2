<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Setup\Model;

use Zend\ServiceManager\ServiceLocatorInterface;

class InstallerFactory
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
     * @param LoggerInterface $log
     * @return Installer
     */
    public function create(LoggerInterface $log)
    {
        return new Installer(
            $this->serviceLocator->get('Magento\Setup\Model\FilePermissions'),
            $this->serviceLocator->get('Magento\Module\Setup\ConfigFactory'),
            $this->serviceLocator->get('Magento\Module\SetupFactory'),
            $this->serviceLocator->get('Magento\Module\ModuleList'),
            $this->serviceLocator->get('Magento\Config\ConfigFactory'),
            $this->serviceLocator->get('Magento\Setup\Model\AdminAccountFactory'),
            $log
        );
    }
}
