<?php
/**
 * Backend Session configuration object
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\Session\Config;

/**
 * Magento Backend session configuration
 *
 * @method Config setSaveHandler()
 */
class AdminConfig extends Config
{
    /**
     * Configuration for admin session name
     */
    const SESSION_NAME_ADMIN = 'admin';

    /**
     * @var FrontNameResolver $frontNameResolver
     */
    protected $frontNameResolver;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Stdlib\String $stringHelper
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param string $scopeType
     * @param FrontNameResolver $frontNameResolver
     * @param string $saveMethod
     * @param null|string $savePath
     * @param null|string $cacheLimiter
     * @param string $lifetimePath
     * @param string $sessionName
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\String $stringHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Filesystem $filesystem,
        $scopeType,
        FrontNameResolver $frontNameResolver,
        $saveMethod = \Magento\Framework\Session\SaveHandlerInterface::DEFAULT_HANDLER,
        $savePath = null,
        $cacheLimiter = null,
        $lifetimePath = self::XML_PATH_COOKIE_LIFETIME,
        $sessionName = self::SESSION_NAME_ADMIN
    ) {
        parent::__construct(
            $scopeConfig,
            $stringHelper,
            $request,
            $filesystem,
            $scopeType,
            $saveMethod,
            $savePath,
            $cacheLimiter,
            $lifetimePath
        );

        $this->frontNameResolver = $frontNameResolver;

        $baseUrl = $this->_httpRequest->getBaseUrl();
        $adminPath = $this->extractAdminPath($baseUrl);
        $this->setCookiePath($adminPath);
        $this->setName($sessionName);
    }

    /**
     * Determine the admin path
     *
     * @param string $baseUrl
     * @return string
     * @throws \InvalidArgumentException
     */
    private function extractAdminPath($baseUrl)
    {
        if (!is_string($baseUrl)) {
            throw new \InvalidArgumentException('Cookie path is not a string.');
        }

        $adminPath = $this->frontNameResolver->getFrontName();

        if (!substr($baseUrl, -1) || ('/' != substr($baseUrl, -1))) {
            $baseUrl = $baseUrl . '/';
        }

        return $baseUrl . $adminPath;
    }
}
