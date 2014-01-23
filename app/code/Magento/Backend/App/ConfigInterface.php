<?php
/**
 * Default application path for backend area
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\App;

/**
 * Backend config accessor
 */
interface ConfigInterface
{
    /**
     * Retrieve config value by path
     *
     * @param string $path
     * @return mixed
     */
    public function getValue($path);

    /**
     * Set config value
     *
     * @param string $path
     * @param mixed $value
     */
    public function setValue($path, $value);

    /**
     * Reinitialize config object
     */
    public function reinit();

    /**
     * Retrieve config flag
     *
     * @param string $path
     * @return bool
     */
    public function isSetFlag($path);
}
