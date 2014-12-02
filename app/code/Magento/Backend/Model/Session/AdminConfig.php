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
use Magento\Framework\Filesystem;
use Magento\Framework\Session\Config;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\DeploymentConfig;

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
     * @param Filesystem $filesystem
     * @param DeploymentConfig $deploymentConfig
     * @param string $scopeType
     * @param FrontNameResolver $frontNameResolver
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param string $lifetimePath
     * @param string $sessionName
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\ValidatorFactory $validatorFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\String $stringHelper,
        \Magento\Framework\App\RequestInterface $request,
        Filesystem $filesystem,
        DeploymentConfig $deploymentConfig,
        $scopeType,
        FrontNameResolver $frontNameResolver,
        \Magento\Framework\StoreManagerInterface $storeManager,
        $lifetimePath = self::XML_PATH_COOKIE_LIFETIME,
        $sessionName = self::SESSION_NAME_ADMIN
    ) {
        parent::__construct(
            $validatorFactory,
            $scopeConfig,
            $stringHelper,
            $request,
            $filesystem,
            $deploymentConfig,
            $scopeType,
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
        $parsedUrl = parse_url($this->_storeManager->getStore()->getBaseUrl());
        $baseUrl = $parsedUrl['path'];
        $adminPath = $this->_frontNameResolver->getFrontName();

        return $baseUrl . $adminPath;
    }
}
