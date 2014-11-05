<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module;

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
     * @return $this
     */
    public function setDataVersion($resName, $version);
}
