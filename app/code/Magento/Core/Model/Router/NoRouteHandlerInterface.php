<?php
/**
 * No route handler interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Router;

interface NoRouteHandlerInterface
{
    /**
     * Check and process no route request
     *
     * @param \Magento\App\RequestInterface $request
     * @return bool
     */
    public function process(\Magento\App\RequestInterface $request);
}
