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
     * View elements factory
     *
     * @var ViewFactory
     */
    protected $viewFactory;

    /**
     * @var ConfigurationFactory
     */
    protected $configurationFactory;

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
     * @param Context $renderContext
     * @param TemplateContext $context
     * @param ViewFactory $viewFactory
     * @param ContentTypeFactory $contentTypeFactory
     * @param ConfigurationFactory $configurationFactory
     * @param array $data
     */
    public function __construct(
        Context $renderContext,
        TemplateContext $context,
        ViewFactory $viewFactory,
        ContentTypeFactory $contentTypeFactory,
        ConfigurationFactory $configurationFactory,
        array $data = []
    ) {
        $this->renderContext = $renderContext;
        $this->viewFactory = $viewFactory;
        $this->contentTypeFactory = $contentTypeFactory;
        $this->assetRepo = $context->getAssetRepository();
        $this->configurationFactory = $configurationFactory;
        $this->configurationBuilder = new ConfigurationBuilder();
        parent::__construct($context, $data);
    }

    /**
     * @param array $arguments
     * @return void
     */
    public function prepare(array $arguments = [])
    {
        if ($arguments) {
            $this->_data = array_merge($this->_data, $arguments);
        }
    }

    /**
     * Render content
     *
     * @return mixed|string
     */
    public function render()
    {
        $result = $this->contentTypeFactory->get($this->renderContext->getAcceptType())->render(
            $this, 
            $this->getContentTemplate()
        );
        return $result;
    }

    /**
     * @param array $arguments
     * @return mixed|string
     */
    public function renderLabel(array $arguments = [])
    {
        $prevArgs = $this->_data;
        $this->_data = array_replace_recursive($this->_data, $arguments);
        $result = $this->contentTypeFactory->get($this->renderContext->getAcceptType())->render($this, $this->getLabelTemplate());
        $this->_data = $prevArgs;

        return $result;
    }

    /**
     * @param $elementName
     * @param array $arguments
     * @return mixed|string
     */
    public function renderElement($elementName, array $arguments)
    {
        $element = $this->viewFactory->get($elementName);
        $element->prepare($arguments);
        return $element->render();
    }

    /**
     * @param $elementName
     * @param array $arguments
     * @return mixed|string
     */
    public function renderElementLabel($elementName, array $arguments)
    {
        return $this->viewFactory->get($elementName)->renderLabel($arguments);
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
     * Getting label template
     *
     * @return string|false
     */
    public function getLabelTemplate()
    {
        return $this->getData('label_template');
    }

    /**
     * Getting content template
     *
     * @return string|false
     */
    public function getContentTemplate()
    {
        return $this->getData('content_template');
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
        return $this->renderContext->getRootView();
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
