<?php
/**
 * Event observers configuration schema locator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Event\Config;

class SchemaLocator implements \Magento\Config\SchemaLocatorInterface
{
    /**
     * Get path to merged config schema
     *
     * @return string
     */
    public function getSchema()
    {
        return realpath(__DIR__ . '/../etc/events.xsd');
    }

    /**
     * Get path to pre file validation schema
     *
     * @return string
     */
    public function getPerFileSchema()
    {
        return $this->getSchema();
    }
}
