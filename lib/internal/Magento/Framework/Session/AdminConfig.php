<?php
/**
 * Backend Session configuration object
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Session;

use Magento\Backend\App\Area\FrontNameResolver;

/**
 * Magento Backend session configuration
 *
 * @method Config setSaveHandler()
 */
class AdminConfig extends Config
{
    /**
     * @var FrontNameResolver $frontNameResolver
     */
    protected $frontNameResolver;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Stdlib\String $stringHelper
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param string $scopeType
     * @param string $saveMethod
     * @param null|string $savePath
     * @param null|string $cacheLimiter
     * @param string $lifetimePath
     * @param FrontNameResolver $frontNameResolver
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\String $stringHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\App\Filesystem $filesystem,
        $scopeType,
        FrontNameResolver $frontNameResolver,
        $saveMethod = \Magento\Framework\Session\SaveHandlerInterface::DEFAULT_HANDLER,
        $savePath = null,
        $cacheLimiter = null,
        $lifetimePath = self::XML_PATH_COOKIE_LIFETIME
    ) {
        // Need to set $this->frontNameResolver prior to calling the parent constructor,
        // as parent constructor calls setCookiePath().
        $this->frontNameResolver = $frontNameResolver;

        parent::__construct(
            $scopeConfig,
            $stringHelper,
            $request,
            $appState,
            $filesystem,
            $scopeType,
            $saveMethod,
            $savePath,
            $cacheLimiter,
            $lifetimePath
        );

        $baseUrl = $this->_httpRequest->getBaseUrl();
        $this->setCookiePath($baseUrl);
    }

    /**
     * Set session.cookie_path
     *
     * @param string $cookiePath
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setCookiePath($cookiePath)
    {
        if (gettype($cookiePath) != 'string') {
            throw new \InvalidArgumentException('Cookie path is not a string');
        }

        $adminPath = $this->frontNameResolver->getFrontName();

        if (!substr($cookiePath, -1) || ('/' != substr($cookiePath, -1))) {
            $cookiePath = $cookiePath . '/';
        }

        return parent::setCookiePath($cookiePath . $adminPath);
    }
}
