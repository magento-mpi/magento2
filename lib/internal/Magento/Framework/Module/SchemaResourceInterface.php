<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module;

/**
 * Schema Resource Model Interface
 */
interface SchemaResourceInterface
{
    /**
     * Set module version into DB
     *
     * @param string $resName
     * @param string $version
     * @return int
     */
    public function setDbVersion($resName, $version);
}