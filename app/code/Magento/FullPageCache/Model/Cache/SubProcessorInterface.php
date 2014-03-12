<?php
/**
 * FPC sub-processor interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model\Cache;

interface SubProcessorInterface
{
    /**
     * Check if request can be cached
     *
     * @param \Magento\App\RequestInterface $request
     * @return bool
     */
    public function allowCache(\Magento\App\RequestInterface $request);

    /**
     * Replace block content to placeholder replacer
     *
     * @param string $content
     * @return string
     */
    public function replaceContentToPlaceholderReplacer($content);

    /**
     * Prepare response body before caching
     *
     * @param \Magento\App\ResponseInterface $response
     * @return string
     */
    public function prepareContent(\Magento\App\ResponseInterface $response);

    /**
     * Return cache page id with application. Depends on GET super global array.
     *
     * @param \Magento\FullPageCache\Model\Processor $processor
     * @return string
     */
    public function getPageIdInApp(\Magento\FullPageCache\Model\Processor $processor);

    /**
     * Return cache page id without application. Depends on GET super global array.
     *
     * @param \Magento\FullPageCache\Model\Processor $processor
     * @return string
     */
    public function getPageIdWithoutApp(\Magento\FullPageCache\Model\Processor $processor);
}
