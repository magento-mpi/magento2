<?php
/**
 * Response redirect interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Response;

interface RedirectInterface
{
    const PARAM_NAME_REFERER_URL        = 'referer_url';
    const PARAM_NAME_ERROR_URL          = 'error_url';
    const PARAM_NAME_SUCCESS_URL        = 'success_url';

    /**
     * Identify referer url via all accepted methods (HTTP_REFERER, regular or base64-encoded request param)
     *
     * @return string
     */
    public function getRefererUrl();

    /**
     * Set referer url for redirect in response
     *
     * @param   string $defaultUrl
     * @return  string
     */
    public function getRedirectUrl($defaultUrl = null);

    /**
     * Redirect to error page
     *
     * @param string $defaultUrl
     * @return  string
     */
    public function error($defaultUrl);

    /**
     * Redirect to success page
     *
     * @param string $defaultUrl
     * @return string
     */
    public function success($defaultUrl);

    /**
     * Set redirect into response
     *
     * @param \Magento\App\ResponseInterface $response
     * @param string $path
     * @param array $arguments
     */
    public function redirect(\Magento\App\ResponseInterface $response, $path, $arguments = array());
}
