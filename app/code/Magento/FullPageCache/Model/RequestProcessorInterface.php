<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model;

interface RequestProcessorInterface
{
    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\App\ResponseInterface $response
     * @param $content
     * @return mixed
     */
    public function extractContent(
        \Magento\App\RequestInterface $request,
        \Magento\App\ResponseInterface $response,
        $content
    );
}
