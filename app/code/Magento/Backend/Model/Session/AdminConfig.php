<?php
/**
 * Backend Session configuration object
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Session;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\Session\Config;
use Magento\Framework\UrlInterface;

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
     * @var FrontNameResolver
     */
    protected $_frontNameResolver;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\ValidatorFactory $validatorFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Stdlib\String $stringHelper
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param string $scopeType
     * @param FrontNameResolver $frontNameResolver
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param string $saveMethod
     * @param null|string $savePath
     * @param null|string $cacheLimiter
     * @param string $lifetimePath
     * @param string $sessionName
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\ValidatorFactory $validatorFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\String $stringHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Filesystem $filesystem,
        $scopeType,
        FrontNameResolver $frontNameResolver,
        \Magento\Framework\StoreManagerInterface $storeManager,
        $saveMethod = \Magento\Framework\Session\SaveHandlerInterface::DEFAULT_HANDLER,
        $savePath = null,
        $cacheLimiter = null,
        $lifetimePath = self::XML_PATH_COOKIE_LIFETIME,
        $sessionName = self::SESSION_NAME_ADMIN
    ) {
        parent::__construct(
            $validatorFactory,
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

        $this->_frontNameResolver = $frontNameResolver;
        $this->_storeManager = $storeManager;
        $adminPath = $this->extractAdminPath();
        $this->setCookiePath($adminPath);
        $this->setName($sessionName);
    }

    /**
     * Determine the admin path
     *
     * @return string
     */
    private function extractAdminPath()
    {
        $type = strpos($this->_httpRequest->getBaseUrl(), \Magento\Framework\App\Filesystem::PUB_DIR) ?
            UrlInterface::URL_TYPE_STATIC : UrlInterface::URL_TYPE_LINK;
        $parsedUrl = parse_url($this->_storeManager->getStore()->getBaseUrl($type));
        $baseUrl = $parsedUrl['path'];
        $adminPath = $this->_frontNameResolver->getFrontName();

        return $baseUrl . $adminPath;
    }
}
