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
     * @var Render
     */
    protected $render;

    /**
     * Data Namespace
     *
     * @var string
     */
    protected $namespace;

    /**
     * Constructor
     *
     * @param ConfigurationStorage $configurationStorage
     * @param ConfigurationStorageBuilder $configurationStorageBuilder
     * @param RequestInterface $request
     */
    public function __construct(
        ConfigurationStorage $configurationStorage,
        ConfigurationStorageBuilder $configurationStorageBuilder,
        RequestInterface $request
    ) {
        $this->configurationStorageBuilder = $configurationStorageBuilder;
        $this->configurationStorage = $configurationStorage;
        $this->request = $request;
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
     * Set Render
     *
     * @param Render $render
     */
    public function setRender(Render $render)
    {
        $this->render = $render;
    }

    /**
     * Get Render
     *
     * @return Render
     */
    public function getRender()
    {
        return $this->render;
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
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Get root view
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
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
