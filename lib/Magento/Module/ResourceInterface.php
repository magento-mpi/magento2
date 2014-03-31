<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module;

/**
 * Resource Model Interface
 *
 * @category    Magento
 * @package     Magento_Module
 * @author      Magento Core Team <core@magentocommerce.com>
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
