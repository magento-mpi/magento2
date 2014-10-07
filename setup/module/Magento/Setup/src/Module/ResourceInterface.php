<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Module;

/**
 * Resource Model Interface
 */
interface ResourceInterface
{
    /**
     * Get Module version from DB
     *
     * @param string $resName
     * @return false|string
     */
    public function getDbVersion($resName);

    /**
     * Set module version into DB
     *
     * @param string $resName
     * @param string $version
     * @return int
     */
    public function setDbVersion($resName, $version);
}
