<?php
/**
 * Application entry point. Bootstraps and runs application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

interface EntryPointInterface
{
    /**
     * @param string $applicationName
     * @param array $arguments
     * @return void
     */
    public function run($applicationName, array $arguments = array());
}
