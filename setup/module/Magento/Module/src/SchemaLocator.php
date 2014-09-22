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

use Magento\Config\SchemaLocatorInterface;
use Magento\Config\ConfigFactory;

class SchemaLocator implements SchemaLocatorInterface
{
    /**
     * @var ConfigFactory
     */
    protected $configFactory;

    /**
     * @var string
     */
    protected $schemaName;

    /**
     * @param ConfigFactory $configFactory
     * @param string $schemaName
     */
    public function __construct(
        ConfigFactory $configFactory,
        $schemaName = 'module.xsd'
    ) {
        $this->configFactory = $configFactory;
        $this->config = $this->configFactory->create();
        $this->schemaName = $schemaName;
    }

    /**
     * Get path to merged config schema
     *
     * @return string
     */
    public function getSchema()
    {
        $path = $this->config->magento->basePath
            . $this->config->magento->filesystem->framework
            . 'Module/etc/' . $this->schemaName;

        return realpath($path);
    }
}
