<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Asset\Repository;
use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ConfigFactory;
use Magento\Framework\View\Element\UiComponent\ConfigInterface;
use Magento\Framework\View\Element\UiComponent\ConfigBuilderInterface;
use Magento\Ui\DataProvider\Factory as DataProviderFactory;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Abstract class AbstractView
 */
abstract class AbstractView extends Template implements UiComponentInterface
{
    /**
     * @var ConfigBuilderInterface
     */
    protected $configBuilder;

    /**
     * View configuration data
     *
     * @var ConfigInterface
     */
    protected $config;

    /**
     * Render context
     *
     * @var Context
     */
    protected $renderContext;

    /**
     * @var ConfigFactory
     */
    protected $configFactory;

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
     * Data provider factory
     *
     * @var DataProviderFactory
     */
    protected $dataProviderFactory;

    /**
     * Constructor
     *
     * @param TemplateContext $context
     * @param Context $renderContext
     * @param ContentTypeFactory $contentTypeFactory
     * @param ConfigFactory $configFactory
     * @param ConfigBuilderInterface $configBuilder
     * @param DataProviderFactory $dataProviderFactory
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Context $renderContext,
        ContentTypeFactory $contentTypeFactory,
        ConfigFactory $configFactory,
        ConfigBuilderInterface $configBuilder,
        DataProviderFactory $dataProviderFactory,
        array $data = []
    ) {
        $this->renderContext = $renderContext;
        $this->contentTypeFactory = $contentTypeFactory;
        $this->assetRepo = $context->getAssetRepository();
        $this->configFactory = $configFactory;
        $this->configBuilder = $configBuilder;
        $this->dataProviderFactory = $dataProviderFactory;
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
        return $this->contentTypeFactory->get($this->renderContext->getAcceptType())
            ->render($this, $this->getContentTemplate());
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
     * @param string $elementName
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
     * Get name component instance
     *
     * @return string
     */
    public function getName()
    {
        return $this->config->getName();
    }

    /**
     * Get parent name component instance
     *
     * @return string
     */
    public function getParentName()
    {
        return $this->config->getParentName();
    }

    /**
     * Get configuration builder
     *
     * @return ConfigBuilderInterface
     */
    public function getConfigBuilder()
    {
        return $this->configBuilder;
    }

    /**
     * Set component configuration
     *
     * @param ConfigInterface $config
     * @return void
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Get component configuration
     *
     * @return ConfigInterface
     */
    public function getConfig()
    {
        return $this->config;
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
     * @return \Magento\Ui\ContentType\ContentTypeInterface
     */
    protected function getRenderEngine()
    {
        return $this->contentTypeFactory->get($this->renderContext->getAcceptType());
    }

    /**
     * Create
     */
    protected function createDataProviders()
    {
        if ($this->hasData('data_provider_pool')) {
            foreach ($this->getData('data_provider_pool') as $name => $config) {
                $config['arguments'] = empty($config['arguments']) ? [] : $config['arguments'];
                $meta = $this->dataProviderFactory->create($config['class'], $config['arguments'])->getMeta();
                $this->renderContext->getStorage()->addMeta($name, $meta);
            }
        }
    }
}
