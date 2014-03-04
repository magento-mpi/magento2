<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Theme\Model\Url\Plugin;

use Magento\Code\Plugin\InvocationChain;

/**
 * Plugin that activates signing of static file URLs with corresponding deployment version
 */
class Signature
{
    /**
     * XPath for configuration setting of signing static files
     */
    const XML_PATH_STATIC_FILE_SIGNATURE = 'dev/static/sign';

    /**
     * Template of signature component of URL, parametrized with the deployment version of static files
     */
    const SIGNATURE_TEMPLATE = 'version%s';

    /**
     * @var \Magento\View\Url\ConfigInterface
     */
    private $config;

    /**
     * @var \Magento\App\View\Deployment\Version
     */
    private $deploymentVersion;

    /**
     * @param \Magento\View\Url\ConfigInterface $config
     * @param \Magento\App\View\Deployment\Version $deploymentVersion
     */
    public function __construct(
        \Magento\View\Url\ConfigInterface $config,
        \Magento\App\View\Deployment\Version $deploymentVersion
    ) {
        $this->config = $config;
        $this->deploymentVersion = $deploymentVersion;
    }

    /**
     * Append signature to rendered base URL for static view files
     *
     * @param array $methodArguments
     * @param InvocationChain $invocationChain
     * @return string
     * @see \Magento\Url\ScopeInterface::getBaseUrl()
     */
    public function aroundGetBaseUrl(array $methodArguments, InvocationChain $invocationChain)
    {
        $baseUrl = $invocationChain->proceed($methodArguments);
        $urlType = isset($methodArguments[0]) ? $methodArguments[0] : '';
        if ($urlType == \Magento\UrlInterface::URL_TYPE_STATIC && $this->isUrlSignatureEnabled()) {
            $baseUrl .= $this->renderUrlSignature() . '/';
        }
        return $baseUrl;
    }

    /**
     * Whether signing of URLs is enabled or not
     *
     * @return bool
     */
    protected function isUrlSignatureEnabled()
    {
        return (bool)$this->config->getValue(self::XML_PATH_STATIC_FILE_SIGNATURE);
    }

    /**
     * Render URL signature from the template
     *
     * @return string
     */
    protected function renderUrlSignature()
    {
        return sprintf(self::SIGNATURE_TEMPLATE, $this->deploymentVersion->getValue());
    }
}
