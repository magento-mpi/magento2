<?php
/**
 * PATH_INFO processor
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Request;

interface PathInfoProcessorInterface
{
    /**
     * Process Request path info
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param string $pathInfo
     * @return string
     */
    public function process(\Magento\Framework\App\RequestInterface $request, $pathInfo);
}
