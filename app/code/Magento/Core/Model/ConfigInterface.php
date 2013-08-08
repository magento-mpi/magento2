<?php
/**
 * Configuration model interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_Core_Model_ConfigInterface
{
    /**
     * Get configuration node
     *
     * @param string $path
     * @return Magento_Simplexml_Element
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
     * Returns nodes found by xpath expression
     *
     * @param string $xpath
     * @return array
     */
    public function getXpath($xpath);

    /**
     * Reinitialize config object
     */
    public function reinit();
}
