<?php
/**
 * Privacy header plugin
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\App\Action\Plugin;

class PrivacyHeader
{
    /**
     * Add HTTP header to response that allows browsers accept third-party cookies
     *
     * @param \Magento\Framework\App\ActionInterface $subject
     * @param \Magento\Framework\App\ResponseInterface $response
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDispatch(\Magento\Framework\App\ActionInterface $subject, $response)
    {
        if ($response) {
            $response->setHeader("P3P", 'CP="CAO PSA OUR"', true);
        }
        return $response;
    }
}
