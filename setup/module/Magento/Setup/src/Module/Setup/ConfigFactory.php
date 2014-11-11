<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Setup\Module\Setup;

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
     * @param string[] $data
     * @return Config
     */
    public function create(array $data = [])
    {
        return new Config(
            $this->serviceLocator->get('Magento\Framework\Filesystem'),
            $data
        );
    }
}
