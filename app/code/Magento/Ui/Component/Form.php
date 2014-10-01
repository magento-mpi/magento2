<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component;

use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\View\Element\UiComponent\ConfigBuilderInterface;
use Magento\Framework\View\Element\UiComponent\ConfigFactory;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Ui\DataProvider\Factory as DataProviderFactory;
use Magento\Framework\View\Element\UiElementFactory;

/**
 * Class Form
 */
class Form extends AbstractView
{
    /**
     * @var UiElementFactory
     */
    protected $uiElementFactory;

    /**
     * @param TemplateContext $context
     * @param Context $renderContext
     * @param ContentTypeFactory $contentTypeFactory
     * @param ConfigFactory $configFactory
     * @param ConfigBuilderInterface $configBuilder
     * @param DataProviderFactory $dataProviderFactory
     * @param UiElementFactory $uiElementFactory
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Context $renderContext,
        ContentTypeFactory $contentTypeFactory,
        ConfigFactory $configFactory,
        ConfigBuilderInterface $configBuilder,
        DataProviderFactory $dataProviderFactory,
        UiElementFactory $uiElementFactory,
        array $data = []
    ) {
        $this->uiElementFactory = $uiElementFactory;
        parent::__construct($context, $renderContext, $contentTypeFactory, $configFactory, $configBuilder,
            $dataProviderFactory, $data);
    }

    /**
     * Prepare component data
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->configFactory->create(
            [
                'name' => $this->renderContext->getNamespace() . '_' . $this->getNameInLayout(),
                'parentName' => $this->renderContext->getNamespace(),
            ]
        );

        $this->setConfig($config);
        $this->renderContext->getStorage()->addComponentsData($config);

        $this->createDataProviders();
        $this->renderContext->getStorage()->addMeta($this->getName(), $this->getData('meta'));
        $this->createElements();
        $this->setRenderLayout();
    }

    /**
     * @return array|null
     */
    public function getMeta()
    {
        return $this->renderContext->getStorage()->getMeta($this->getName());
    }

    /**
     * @param array $fieldData
     * @return string
     */
    public function getFieldType(array $fieldData)
    {
        $type = '';
        if (isset($fieldData['data_type'])) {
            $type = $fieldData['data_type'];
        } else {
            if (isset($fieldData['frontend_input'])) {
                $type = $fieldData['frontend_input'];
            }
        }

        return $type;
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
     * Set render layout
     *
     * @return void
     */
    protected function setRenderLayout()
    {
        if ($this->hasData('layout')) {
            $layoutElement = $this->getLayout()->getBlock($this->getData('layout'));
            if ($layoutElement !== false) {
                /** @var RenderLayoutInterface $layoutElement */
                $layoutElement->prepare();
                $layoutElement->setElements($this->getElements());
                $this->setElements([$layoutElement]);
            }
        }
    }

    /**
     * @return void
     */
    protected function createElements()
    {
        if ($this->hasData('data_provider_pool')) {
            foreach ($this->getData('data_provider_pool') as $name => $config) {
                $dataProvider = $this->getRenderContext()->getStorage()->getDataProvider($name);
                $data = $dataProvider->getData();
                foreach ($dataProvider->getMeta() as $metaData) {
                    $index = $this->getFieldIndex($metaData);
                    $metaData['value'] = isset($data[$index]) ? $data[$index] : null;
                    try {
                        $this->elements[] = $this->uiElementFactory->create($this->getFieldType($metaData), $metaData);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }
    }

    /**
     * @param array $fieldData
     * @return string
     */
    public function getFieldIndex(array $fieldData)
    {
        $type = '';
        if (isset($fieldData['attribute_code'])) {
            $type = $fieldData['attribute_code'];
        } else {
            if (isset($fieldData['index'])) {
                $type = $fieldData['index'];
            }
        }

        return $type;
    }
}
