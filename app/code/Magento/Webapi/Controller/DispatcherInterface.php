<?php
/**
 * Abstract dispatcher for web API requests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller;

interface DispatcherInterface
{
    /**
     * Dispatch request.
     *
     * @return \Magento\Webapi\Controller\DispatcherInterface
     */
    public function dispatch();
}
