<?php
/**
 * Saas Shutdown Handler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Helper_Shutdown_Handler extends Mage_Core_Helper_Data
{
    /**
     * Register shutdown function for processing PHP Fatal Errors which had occurred during specified process
     *
     * @param string|object $object
     * @param string $method
     * @throws InvalidArgumentException
     */
    public function registerShutdownFunction($object, $method)
    {
        if (!method_exists($object, $method)) {
            throw new InvalidArgumentException("The object {$object} doesn't contain a method as {$method}");
        }

        register_shutdown_function(array($object, $method));
    }
}
