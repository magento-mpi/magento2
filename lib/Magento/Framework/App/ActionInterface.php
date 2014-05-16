<?php
/**
 * Magento application action
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

interface ActionInterface
{
    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function dispatch(RequestInterface $request);

    /**
     * Get Response object
     *
     * @return ResponseInterface
     */
    public function getResponse();
}
