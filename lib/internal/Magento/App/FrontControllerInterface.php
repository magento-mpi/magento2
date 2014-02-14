<?php
/**
 * Application front controller responsible for dispatching application requests
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

interface FrontControllerInterface
{
    /**
     * Dispatch application action
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function dispatch(RequestInterface $request);
}
