<?php
/**
 * Routes configuration schema locator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Route\Config;

class SchemaLocator implements \Magento\Framework\Config\SchemaLocatorInterface
{
    /**
     * Get path to merged config schema
     *
     * @return string
     */
    public function getSchema()
    {
        return realpath(__DIR__ . '/../../etc/routes_merged.xsd');
    }

    /**
     * Get path to pre file validation schema
     *
     * @return string
     */
    public function getPerFileSchema()
    {
        return realpath(__DIR__ . '/../../etc/routes.xsd');
    }
}
