<?php
/**
 * Resource resolver is used to retrieve a list of resources declared by module
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\Module;

interface ResourceResolverInterface
{
    /**
     * Retrieve the list of resources declared by module
     *
     * @param string $moduleName
     * @return array
     */
    function getResourceList($moduleName);
}
