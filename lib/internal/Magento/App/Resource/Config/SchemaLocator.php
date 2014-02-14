<?php
/**
 * Resource configuration schema locator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Resource\Config;

class SchemaLocator implements \Magento\Config\SchemaLocatorInterface
{
    /**
     * Get path to per file validation schema
     *
     * @return string|null
     */
    public function getPerFileSchema()
    {
        return realpath(__DIR__ . '/../../etc/resources.xsd');
    }

    /**
     * Get path to merged config schema
     *
     * @return string|null
     */
    public function getSchema()
    {
        return $this->getPerFileSchema();
    }
}
