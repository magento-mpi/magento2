<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui;

use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\ContentType\Builders\ConfigurationStorageBuilder;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class Context
 */
class Context extends Registry
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * Configuration storage builder
     *
     * @var ConfigurationStorageBuilder
     */
    protected $configurationStorageBuilder;

    /**
     * Configuration storage
     *
     * @var ConfigurationStorageInterface
     */
    protected $configurationStorage;

    /**
     * Application request
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     * Accept type
     *
     * @var string
     */
    protected $acceptType;

    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * Root view element
     *
     * @var AbstractView
     */
    protected $rootView;

    /**
     * Constructor
     *
     * @param ConfigInterface $config
     * @param ConfigurationStorage $configurationStorage
     * @param ConfigurationStorageBuilder $configurationStorageBuilder
     * @param TemplateContext $context
     */
    public function __construct(
        ConfigInterface $config,
        ConfigurationStorage $configurationStorage,
        ConfigurationStorageBuilder $configurationStorageBuilder,
        TemplateContext $context
    ) {
        $this->config = $config;
        $this->configurationStorageBuilder = $configurationStorageBuilder;
        $this->configurationStorage = $configurationStorage;
        $this->request = $context->getRequest();
        $this->setAcceptType();
    }

    /**
     * Getting requested accept type
     *
     * @return string
     */
    protected function setAcceptType()
    {
        $this->acceptType = 'xml';

        $rawAcceptType = $this->request->getHeader('Accept');
        if (strpos($rawAcceptType, 'json') !== false) {
            $this->acceptType = 'json';
        } elseif (strpos($rawAcceptType, 'html') !== false) {
            $this->acceptType = 'html';
        }
    }

    /**
     * Getting accept type
     *
     * @return string
     */
    public function getAcceptType()
    {
        return $this->acceptType;
    }

    /**
     * Set root layout
     *
     * @param LayoutInterface $layout
     */
    public function setPageLayout(LayoutInterface $layout)
    {
        $this->layout = $layout;
    }

    /**
     * Get root layout
     *
     * @return LayoutInterface
     */
    public function getPageLayout()
    {
        return $this->layout;
    }

    /**
     * Set root view
     *
     * @param AbstractView $view
     */
    public function setRootView(AbstractView $view)
    {
        $this->rootView = $view;
    }

    /**
     * Get root view
     *
     * @return AbstractView
     */
    public function getRootView()
    {
        return $this->rootView;
    }

    /**
     * Getting all request data
     *
     * @return mixed
     */
    public function getRequestParams()
    {
        return $this->request->getParams();
    }

    /**
     * Getting data according to the key
     *
     * @param string $key
     * @param mixed|null $defaultValue
     * @return mixed
     */
    public function getRequestParam($key, $defaultValue = null)
    {
        return $this->request->getParam($key, $defaultValue);
    }

    /**
     * Get storage configuration
     *
     * @return ConfigurationStorageInterface
     */
    public function getStorage()
    {
        return $this->configurationStorage;
    }

    /**
     * Get configuration builder
     *
     * @return ConfigurationStorageBuilder
     */
    public function getConfigurationBuilder()
    {
        return $this->configurationStorageBuilder;
    }

    /**
     * Get configuration object
     *
     * @return ConfigInterface
     */
    public function getConfig()
    {
        return $this->config;
    }
}
