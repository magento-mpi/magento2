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
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class AbstractView
 */
abstract class AbstractView extends Template implements ViewInterface
{
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
    protected $factory;

    /**
     * Asset service
     *
     * @var Repository
     */
    protected $assetRepo;

    /**
     * Data view
     *
     * @var array
     */
    protected $viewData = [];

    /**
     * View configuration data
     *
     * @var array
     */
    protected $viewConfiguration = [];

    /**
     * Global config storage
     *
     * @var array
     */
    protected $globalConfig = [];

    /**
     * @param Context $renderContext
     * @param TemplateContext $context
     * @param ContentTypeFactory $factory
     * @param array $data
     */
    public function __construct(
        Context $renderContext,
        TemplateContext $context,
        ContentTypeFactory $factory,
        array $data = []
    ) {
        $this->renderContext = $renderContext;
        $this->factory = $factory;
        $this->assetRepo = $context->getAssetRepository();
        parent::__construct($context, $data);
    }

    /**
     * @param array $arguments
     * @param string $acceptType
     * @return mixed|string
     */
    public function render(array $arguments = [], $acceptType = 'html')
    {
        $prevArgs = $this->_data;
        $this->_data = array_replace_recursive($this->_data, $arguments);
        $result = $this->factory->get($acceptType)->render($this, $this->getTemplate());
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
     * Getting template
     *
     * @return string|false
     */
    public function getTemplate()
    {
        return $this->getData('template');
    }

    /**
     * Getting view data array
     *
     * @return array
     */
    public function getViewData()
    {
        return (array)$this->viewData;
    }

    /**
     * Getting configuration settings array
     *
     * @return array
     */
    public function getViewConfiguration()
    {
        return (array)$this->viewConfiguration;
    }

    /**
     * Getting JSON configuration data
     *
     * @return string
     */
    public function getConfigurationJson()
    {
        return json_encode($this->getViewConfiguration());
    }

    /**
     * Getting render engine
     *
     * @return ContentType\ContentTypeInterface
     */
    protected function getRenderEngine()
    {
        return $this->factory->get($this->renderContext->getAcceptType());
    }

    /**
     * Getting name component instance
     *
     * @return string
     */
    public function getName()
    {
        return isset($this->viewConfiguration['name']) ? $this->viewConfiguration['name'] : null;
    }

    /**
     * Getting parent name component instance
     *
     * @return string
     */
    public function getParentName()
    {
        return isset($this->viewConfiguration['parent_name']) ? $this->viewConfiguration['parent_name'] : null;
    }

    /**
     * Add data into configuration element view
     *
     * @param AbstractView $view
     * @param array $data
     */
    public function addConfigData(AbstractView $view, array $data)
    {
        $this->globalConfig['config']['components'][$view->getName()] = $data;
    }

    /**
     * Getting JSON global configuration data
     *
     * @return string
     */
    public function getGlobalConfigJson()
    {
        return json_encode($this->globalConfig);
    }
}
