<?php
/**
 * PATH_INFO processor
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Request;

interface PathInfoProcessorInterface
{
    /**
     * Process Request path info
     *
     * @param \Magento\App\RequestInterface $request
     * @param string $pathInfo
     * @return string
     */
    public function process(\Magento\App\RequestInterface $request, $pathInfo);
}