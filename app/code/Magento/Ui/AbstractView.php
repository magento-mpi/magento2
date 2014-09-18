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
 * Abstract class AbstractView
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
     * @param ContentTypeFactory $contentTypeFactory
     * @param ConfigurationFactory $configurationFactory
     * @param array $data
     */
    public function __construct(
        Context $renderContext,
        TemplateContext $context,
        ContentTypeFactory $contentTypeFactory,
        ConfigurationFactory $configurationFactory,
        array $data = []
    ) {
        $this->renderContext = $renderContext;
        $this->contentTypeFactory = $contentTypeFactory;
        $this->assetRepo = $context->getAssetRepository();
        $this->configurationFactory = $configurationFactory;
        $this->configurationBuilder = new ConfigurationBuilder();
        parent::__construct($context, $data);
    }

    /**
     * Update data
     *
     * @param array $arguments
     * @return void
     */
    public function update(array $arguments = [])
    {
        if ($arguments) {
            $this->_data = array_merge_recursive($this->_data, $arguments);
        }
    }

    /**
     * Prepare component data
     *
     * @return void
     */
    public function prepare()
    {
        //
    }

    /**
     * Render content
     *
     * @return string
     */
    public function render()
    {
        $result = $this->contentTypeFactory->get($this->renderContext->getAcceptType())
            ->render($this, $this->getContentTemplate());
        return $result;
    }

    /**
     * Render label
     *
     * @return mixed|string
     */
    public function renderLabel()
    {
        $result = $this->contentTypeFactory->get($this->renderContext->getAcceptType())
            ->render($this, $this->getLabelTemplate());
        return $result;
    }

    /**
     * Render element
     *
     * @param $elementName
     * @param array $arguments
     * @return mixed|string
     */
    public function renderElement($elementName, array $arguments)
    {
        $element = $this->renderContext->getRender()->createUiComponent($elementName);
        $prevData = $element->getData();
        $element->update($arguments);
        $result = $element->render();
        $element->setData($prevData);
        return $result;
    }

    /**
     * Render component label
     *
     * @param string $elementName
     * @param array $arguments
     * @return string
     */
    public function renderElementLabel($elementName, array $arguments)
    {
        $element = $this->renderContext->getRender()->createUiComponent($elementName);
        $prevData = $element->getData();
        $element->update($arguments);
        $result = $element->renderLabel();
        $element->setData($prevData);
        return $result;
    }

    /**
     * Shortcut for rendering as HTML
     * (used for backward compatibility with standard rendering mechanism via layout interface)
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->render();
    }

    /**
     * Getting label template
     *
     * @return string|false
     */
    public function getLabelTemplate()
    {
        return 'Magento_Ui::label/default.phtml';
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
     * Get default parameters
     *
     * @return array
     */
    protected function getDefaultConfiguration()
    {
        return [];
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
