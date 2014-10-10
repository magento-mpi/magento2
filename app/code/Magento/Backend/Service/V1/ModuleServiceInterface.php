<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Service\V1;

interface ModuleServiceInterface
{
    /**
     * Returns an array of enabled modules
     *
     * @return string[]
     */
    public function getModules();
}
