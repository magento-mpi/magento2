<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_FullPageCache_Model_RequestProcessorInterface
{
    /**
     * Extract cached page content
     *
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Controller_Response_Http $response
     * @param string $content
     * @return bool|string
     */
    public function extractContent(
        Zend_Controller_Request_Http $request,
        Zend_Controller_Response_Http $response,
        $content
    );
}
