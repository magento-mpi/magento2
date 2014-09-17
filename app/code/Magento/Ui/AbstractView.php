<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Asset\Repository;
use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Ui\ContentType\Builders\ConfigurationBuilder;
use Magento\Ui\ContentType\Builders\ConfigBuilderInterface;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class AbstractView
 */
abstract class AbstractView extends Template implements ViewInterface
{
    /**
     * @var ConfigBuilderInterface
     */
    protected $configurationBuilder;

    /**
     * Root view component
     *
     * @var ViewInterface
     */
    protected $rootComponent;

    /**
     * View configuration data
     *
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * Render context
     *
     * @var Context
     */
    protected $renderContext;

    /**
     * Content type factory
     *
     * @var ContentTypeFactory
     */
    protected $contentTypeFactory;

    /**
     * Asset service
     *
     * @var Repository
     */
    protected $assetRepo;

    /**
     * Constructor
     *
     * @param TemplateContext $context
     * @param Context $renderContext
     * @param ContentTypeFactory $contentTypeFactory
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Context $renderContext,
        ContentTypeFactory $contentTypeFactory,
        array $data = []
    ) {
        $this->renderContext = $renderContext;
        $this->contentTypeFactory = $contentTypeFactory;
        $this->assetRepo = $context->getAssetRepository();
        $this->configurationBuilder = new ConfigurationBuilder();
        parent::__construct($context, $data);
    }

    /**
     * Render content
     *
     * @param array $arguments
     * @param string $acceptType
     * @return mixed|string
     */
    public function render(array $arguments = [], $acceptType = 'html')
    {
        $prevArgs = $this->_data;
        $this->_data = array_replace_recursive($this->_data, $arguments);
        $result = $this->contentTypeFactory->get($acceptType)->render($this, $this->getTemplate());
        $this->_data = $prevArgs;

        return $result;
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->prepare();

        return parent::_prepareLayout();
    }

    /**
     * Get default parameters
     *
     * @return array
     */
    protected function getDefaultConfiguration()
    {
        return [];
    }

    /**
     * Prepare custom data
     *
     * @return void
     */
    protected function prepare()
    {
        //
    }

    /**
     * Shortcut for rendering as HTML
     * (used for backward compatibility with standard rendering mechanism via layout interface)
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->render([], $this->renderContext->getAcceptType());
    }

    /**
     * Get template
     *
     * @return string|false
     */
    public function getTemplate()
    {
        return $this->getData('template');
    }

    /**
     * Get render engine
     *
     * @return ContentType\ContentTypeInterface
     */
    protected function getRenderEngine()
    {
        return $this->contentTypeFactory->get($this->renderContext->getAcceptType());
    }

    /**
     * @return bool|ViewInterface
     */
    protected function getParentComponent()
    {
        return $this->getParentBlock()->getParentBlock();
    }

    /**
     * Get name component instance
     *
     * @return string
     */
    public function getName()
    {
        return $this->configuration->getName();
    }

    /**
     * Get parent name component instance
     *
     * @return string
     */
    public function getParentName()
    {
        return $this->configuration->getParentName();
    }

    /**
     * Get configuration builder
     *
     * @return ConfigBuilderInterface
     */
    public function getConfigurationBuilder()
    {
        return $this->configurationBuilder;
    }

    /**
     * Get component configuration
     *
     * @return ConfigurationInterface
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Get render context
     *
     * @return Context
     */
    public function getRenderContext()
    {
        return $this->renderContext;
    }
}
