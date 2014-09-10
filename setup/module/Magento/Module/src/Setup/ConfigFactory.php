<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Module\Setup;

use Zend\ServiceManager\ServiceLocatorInterface;

class ConfigFactory
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
     * @param array $data
     * @return Config
     */
    public function create($data = [])
    {
        return new Config(
            $this->serviceLocator->get('Magento\Filesystem\Filesystem'),
            $this->serviceLocator->get('Magento\Framework\Math\Random'),
            $data
        );
    }
}
