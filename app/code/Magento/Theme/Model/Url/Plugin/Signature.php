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
     * @var \Magento\UrlInterface
     */
    private $baseUrl;

    /**
     * @var \Magento\App\View\Deployment\Version
     */
    private $deploymentVersion;

    /**
     * @param \Magento\View\Url\ConfigInterface $config
     * @param \Magento\UrlInterface $baseUrl
     * @param \Magento\App\View\Deployment\Version $deploymentVersion
     */
    public function __construct(
        \Magento\View\Url\ConfigInterface $config,
        \Magento\UrlInterface $baseUrl,
        \Magento\App\View\Deployment\Version $deploymentVersion
    ) {
        $this->config = $config;
        $this->baseUrl = $baseUrl;
        $this->deploymentVersion = $deploymentVersion;
    }

    /**
     * Incorporate signature into rendered URL depending on the configuration
     *
     * @param array $methodArguments
     * @param InvocationChain $invocationChain
     * @return string
     * @see \Magento\View\Url::getViewFileUrl()
     */
    public function aroundGetViewFileUrl(array $methodArguments, InvocationChain $invocationChain)
    {
        $url = $invocationChain->proceed($methodArguments);
        if (!$this->isUrlSignatureEnabled()) {
            return $url;
        }
        $urlParams = isset($methodArguments[1]) ? (array)$methodArguments[1] : array();
        $isSecureUrl = isset($urlParams['_secure']) ? (bool)$urlParams['_secure'] : null;
        $baseUrl = $this->baseUrl->getBaseUrl(array(
            '_type' => \Magento\UrlInterface::URL_TYPE_STATIC,
            '_secure' => $isSecureUrl
        ));
        $signedBaseUrl = $this->renderSignedUrl($baseUrl);
        $url = str_replace($baseUrl, $signedBaseUrl, $url);
        return $url;
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
     * Incorporate deployment version of static files into URL
     *
     * @param string $url
     * @return string
     */
    protected function renderSignedUrl($url)
    {
        $signature = sprintf(self::SIGNATURE_TEMPLATE, $this->deploymentVersion->getValue());
        return $url . $signature . '/';
    }
}
