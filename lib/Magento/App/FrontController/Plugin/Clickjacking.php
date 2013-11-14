<?php
/**
 * Clickjacking protection plugin
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\FrontController\Plugin;

class Clickjacking
{
    /**
     * Process response
     *
     * @param \Magento\App\Response $response
     * @return \Magento\App\Response
     */
    public function afterDispatch(\Magento\App\Response $response)
    {
        if (!$response->getHeader('X-Frame-Options')) {
            $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        }
        return $response;
    }
}
