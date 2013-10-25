<?php
/**
 * Application router list
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

interface RouterListInterface
{
    /**
     * Get list of active routers
     * sorted by sortOrder
     *
     * @return array
     */
    public function getRouters();

    /**
     * Get router by route
     *
     * @param string $routeId
     * @return \Magento\App\Router\AbstractRouter
     */
    public function getRouterByRoute($routeId);

    /**
     * Get router by frontName
     *
     * @param string $frontName
     * @return \Magento\App\Router\AbstractRouter
     */
    public function getRouterByFrontName($frontName);
}