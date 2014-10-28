<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component;

use Magento\Framework\View\Element\UiElementFactory;
use Magento\Ui\DataProvider\Manager;
use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\ConfigFactory;
use Magento\Framework\View\Element\UiComponent\ConfigBuilderInterface;
use Magento\Ui\DataProvider\Factory as DataProviderFactory;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Ui\DataProvider\Metadata;
use Magento\Webapi\Exception;

/**
 * Class Form
 */
class Form extends AbstractView
{
    /**
     * Default form element
     */
    const DEFAULT_FORM_ELEMENT = 'input';

    /**
     * From element map
     *
     * @var array
     */
    protected $formElementMap = [
        'text' => 'input',
        'number' => 'input'
    ];

    /**
     * Ui element builder
     *
     * @var ElementRendererBuilder
     */
    protected $elementRendererBuilder;

    /**
     * @var UiElementFactory
     */
    protected $factory;

    /**
     * Constructor
     *
     * @param TemplateContext $context
     * @param Context $renderContext
     * @param ContentTypeFactory $contentTypeFactory
     * @param ConfigFactory $configFactory
     * @param ConfigBuilderInterface $configBuilder
     * @param DataProviderFactory $dataProviderFactory
     * @param Manager $dataProviderManager
     * @param ElementRendererBuilder $elementRendererBuilder
     * @param UiElementFactory $factory
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Context $renderContext,
        ContentTypeFactory $contentTypeFactory,
        ConfigFactory $configFactory,
        ConfigBuilderInterface $configBuilder,
        DataProviderFactory $dataProviderFactory,
        Manager $dataProviderManager,
        ElementRendererBuilder $elementRendererBuilder,
        UiElementFactory $factory,
        array $data = []
    ) {
        $this->elementRendererBuilder = $elementRendererBuilder;
        $this->factory = $factory;
        parent::__construct(
            $context,
            $renderContext,
            $contentTypeFactory,
            $configFactory,
            $configBuilder,
            $dataProviderFactory,
            $dataProviderManager,
            $data
        );
    }

    /**
     * Prepare component data
     *
     * @return void
     */
    public function prepare()
    {
        $this->registerComponents();

        $layoutSettings = (array) $this->getData('layout');
        $data = [
            'name' => $this->getData('name'),
            'label' => $this->getData('label'),
            'data_sources' => $this->getData('data_sources'),
            'child_blocks' => $this->getLayout()->getChildBlocks($this->getNameInLayout()),
            'configuration' => isset($layoutSettings['configuration'])
                ? $layoutSettings['configuration']
                : []
        ];
        $layoutType = isset($layoutSettings['type'])
            ? $layoutSettings['type']
            : \Magento\Ui\Component\Layout\Tabs::NAME;
        $layout = $this->factory->create(
            $layoutType,
            $data
        );
        $layout->prepare();
        $this->elements[] = $layout;
    }

    /**
     * @return string
     */
    public function getDataScope()
    {
        return $this->getData('name');
    }

    /**
     * Register all UI Components configuration
     *
     * @return void
     */
    protected function registerComponents()
    {
        $this->renderContext->getStorage()->addComponent(
            $this->getData('name'),
            [
                'component' => 'Magento_Ui/js/form/component',
                'config' => [
                    'provider' => $this->getData('name')
                ],
                'deps' => [$this->getData('name')]
            ]
        );
        foreach ($this->getLayout()->getAllBlocks() as $name => $block) {
            if ($block instanceof \Magento\Framework\View\Element\UiComponentInterface) {
                $config = (array)$block->getData('js_config');
                if (!isset($config['extends'])) {
                    $config['extends'] = $this->getData('name');
                }
                $this->renderContext->getStorage()->addComponent($name, $config);
            }
        };
    }

    /**
     * @return string
     */
    public function getSaveAction()
    {
        return $this->getUrl('mui/form/save');
    }

    /**
     * @return string
     */
    public function getValidateAction()
    {
        return $this->getUrl('mui/form/validate');
    }
}
