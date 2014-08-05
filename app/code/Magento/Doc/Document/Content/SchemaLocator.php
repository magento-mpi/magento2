<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Document\Content;

/**
 * Class SchemaLocator
 * @package Magento\Doc\Document\Content
 */
class SchemaLocator implements \Magento\Framework\Config\SchemaLocatorInterface
{
    /**
     * Get path to merged config schema
     *
     * @return string
     */
    public function getSchema()
    {
        return realpath(__DIR__ . '/../../docs/') . '/content.xsd';
    }

    /**
     * Get path to pre file validation schema
     *
     * @return null
     */
    public function getPerFileSchema()
    {
        return null;
    }
}
