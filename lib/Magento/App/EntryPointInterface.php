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
     */
    public function run($applicationName);
} 