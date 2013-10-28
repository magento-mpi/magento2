<?php
/**
 * Routes configuration interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Route;

interface ConfigInterface
{

    /**
     * Fetch routes from configs by area code and router id
     *
     * @param string $routerId
     * @return array
     */
    public function getRoutes($routerId);
}
