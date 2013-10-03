<?php
/**
 * Http request handler interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\HTTP;

interface HandlerInterface
{
    /**
     * Handle http request
     *
     * @param \Zend_Controller_Request_Http $request
     * @param \Zend_Controller_Response_Http $response
     */
    public function handle(\Zend_Controller_Request_Http $request, \Zend_Controller_Response_Http $response);
}
