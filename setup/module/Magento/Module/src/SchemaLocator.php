<?php
/**
 * Modules configuration schema locator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Module;

use Zend\ServiceManager\ServiceLocatorInterface;
use Magento\Config\SchemaLocatorInterface;

class SchemaLocator implements SchemaLocatorInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @var string
     */
    protected $schemaName;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $schemaName
     */
    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        $schemaName = 'module.xsd'
    ) {
        $this->serviceLocator = $serviceLocator;
        $this->schemaName = $schemaName;
        $this->configuration = $this->serviceLocator->get('config')['parameters'];
    }

    /**
     * Get path to merged config schema
     *
     * @return string
     */
    public function getSchema()
    {
        $path = $this->configuration['magento']['base_path']
            . $this->configuration['magento']['filesystem']['framework']
            . 'Module/etc/' . $this->schemaName;

        return realpath($path);
    }
}
