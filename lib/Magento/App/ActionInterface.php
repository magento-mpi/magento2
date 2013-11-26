<?php
/**
 * Magento application action
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

interface ActionInterface
{
    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function dispatch(RequestInterface $request);
}
