<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_Di_Definition_CompilerDefinition
{
    /**
     * Add directory
     *
     * @param string $directory
     */
    public function addDirectory($directory);

    /**
     * Compile
     */
    public function compile();

    /**
     * Get definition as array
     *
     * @return array
     */
    public function toArray();
}
