<?php
/**
 * Application response
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

interface ResponseInterface
{
    /**
     * Send response to client
     *
     * @return int|void
     */
    public function sendResponse();
}