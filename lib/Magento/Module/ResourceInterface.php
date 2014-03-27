<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module;

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

    /**
     * Get resource data version
     *
     * @param string $resName
     * @return string|false
     */
    public function getDataVersion($resName);

    /**
     * Specify resource data version
     *
     * @param string $resName
     * @param string $version
     */
    public function setDataVersion($resName, $version);
}