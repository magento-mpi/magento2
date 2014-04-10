<?php
/**
 * Clickjacking protection plugin
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\FrontController\Plugin;

class Clickjacking
{
    /**
     * Process response
     *
     * @param \Magento\Framework\App\FrontController $subject
     * @param \Magento\Framework\App\ResponseInterface $response
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDispatch(
        \Magento\Framework\App\FrontController $subject,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        if (!$response->getHeader('X-Frame-Options')) {
            $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        }
        return $response;
    }
}
