<?php
/**
 * HTTP response interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Response;

interface HttpInterface extends \Magento\Framework\App\ResponseInterface
{
    /**
     * Set HTTP response code
     *
     * @param int $code
     * @return void
     */
    public function setHttpResponseCode($code);
}
