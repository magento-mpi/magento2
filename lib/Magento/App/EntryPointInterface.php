<?php
/**
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
     */
    public function run($applicationName, array $arguments = array());
}
