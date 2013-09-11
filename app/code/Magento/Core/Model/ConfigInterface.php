<?php
/**
 * Configuration model interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model;

interface ConfigInterface
{
    /**
     * Get configuration node
     *
     * @param string $path
     * @return \Magento\Simplexml\Element
     */
    public function getNode($path = null);

    /**
     * Create node by $path and set its value
     *
     * @param string $path separated by slashes
     * @param string $value
     * @param boolean $overwrite
     */
    public function setNode($path, $value, $overwrite = true);

    /**
     * Reinitialize config object
     */
    public function reinit();
}
