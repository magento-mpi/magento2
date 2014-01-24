<?php
/**
 * Hierarchy interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config\Scope;

interface HierarchyInterface
{
    /**
     * Get Hierarchy by Scope
     *
     * @param string $scope
     * @return array
     */
    public function getHierarchy($scope);
}
