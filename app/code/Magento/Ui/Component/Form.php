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
        parent::prepare();
        //$this->prepareConfiguration($config, $this->getData('name'));

        $this->prepareConfigForJS();
//        $this->renderContext->getStorage()->addMeta($this->getName(), $this->getData('meta'));
//        $this->createElements();
    }

    public function prepareConfigForJS()
    {
        $this->renderContext->getStorage()->addComponent($this->getData('name'), [
                'path' => 'Magento_Ui/js/form',
                'source' => $this->getData('name'),
                'name' => $this->getData('name')
            ]);
        $this->renderContext->getStorage()->addComponent($this->getData('name') . '_tabs', [
                'name' => $this->getData('name') . '_tabs',
                'path' => 'Magento_Ui/js/form/components/tab/group',
                'source' => $this->getData('name')
            ]);
        $this->renderContext->getStorage()->addComponent($this->getData('name') . '_fieldsets', [
                'name' => $this->getData('name') . '_fieldsets',
                'path' => 'Magento_Ui/js/form/components/fieldset',
                'source' => $this->getData('name')
            ]);
        $this->renderContext->getStorage()->addComponent($this->getData('name') . '_areas', [
                'name' => $this->getData('name') . '_areas',
                'path' => 'Magento_Ui/js/form/components/area',
                'source' => $this->getData('name')
            ]);
        $this->renderContext->getStorage()->addComponent($this->getData('name') . '_groups', [
                'name' => $this->getData('name') . '_groups',
                'path' => 'Magento_Ui/js/form/components/group',
                'source' => $this->getData('name')
            ]);
        if ($this->hasData('data_sources')) {
            foreach ($this->getData('data_sources') as $name => $dataSource) {

                $layoutGroups = [];
                $layoutFieldsets = [];
                $layoutAreas = [];
                $layoutTabs = ['default' => ['label' => 'Tab Group']];

                $id = $this->renderContext->getRequestParam('id');
                $data = $id ? $this->dataManager->getData($dataSource, ['entity_id' => $id]) : [];
                $meta = $this->dataManager->getMetadata($dataSource);
                $children = $meta->get(Metadata::CHILD_DATA_SOURCES);


                $preparedMeta = [];
                foreach ($meta as $key => $value) {
                    if ($key != Metadata::CHILD_DATA_SOURCES) {
                        $preparedMeta[$dataSource][$key] = $value;
                        $layoutGroups[$key]['injections'][] = $dataSource . '.' . $key;
                        $layoutFieldsets[$dataSource]['injections'][] = $this->getData('name') . '_groups.' . $key;
                    }
                }
                $layoutFieldsets[$dataSource]['label'] = $dataSource;
                $layoutAreas[$dataSource]['injections'][] = $this->getData('name') . '_fieldsets.' . $dataSource;
                $layoutTabs['default']['items'][$dataSource] = ['name' => $dataSource, 'label' => $dataSource, 'active' => true];
                foreach ($children as $childName) {
                    $childMeta = $this->dataManager->getMetadata($childName);
                    foreach ($childMeta as $key => $value) {
                        $preparedMeta[$childName][$key] = $value;
                        $layoutGroups[$key]['injections'][] = $childName . '.' . $key;
                        $layoutFieldsets[$childName]['injections'][] = $this->getData('name') . '_groups.' . $key;
                    }
                    $layoutFieldsets[$childName]['label'] = $childName;
                    $layoutAreas[$childName]['injections'][] = $this->getData('name') . '_fieldsets.' . $childName;
                    $layoutTabs['default']['items'][$childName] = ['name' => $childName, 'label' => $childName];
                }

                //Add child blocks content
                foreach ($this->getLayout()->getChildBlocks($this->getNameInLayout()) as $childBlock) {
                    if (!($childBlock instanceof \Magento\Backend\Block\Widget\Tab\TabInterface)) {
                        throw new \Exception($childBlock->getNameInLayout() . 'should implement TabInterface');
                    }
                    $layoutTabs['default']['items'][$childBlock->getNameInLayout()] = [
                        'name' => $childBlock->getNameInLayout(),
                        'label' => $childBlock->getTabTitle(),
                        'ajax' => $this->getUrl(
                            'mui/form/fieldset',
                            [
                                'component' => 'form',
                                'name' => $this->getData('name'),
                                'container' => $childBlock->getNameInLayout()
                            ]
                        )
                    ];
                }

                $layoutTabs['default']['items']['test_tab_with_content'] = [
                    'name' => 'test_tab',
                    'label' => 'Test tab with content',
                    'content' => 'Hello World!',
                    'id' => $id
                ];

                $this->renderContext->getStorage()->addLayoutNode(
                    $this->getData('name') . '_groups',
                    $layoutGroups
                );
                $this->renderContext->getStorage()->addLayoutNode(
                    $this->getData('name') . '_fieldsets',
                    $layoutFieldsets
                );
                $this->renderContext->getStorage()->addLayoutNode(
                    $this->getData('name') . '_tabs',
                    $layoutTabs
                );
                $this->renderContext->getStorage()->addLayoutNode(
                    $this->getData('name') . '_areas',
                    $layoutAreas
                );

                $preparedData = [];
                foreach ($data[0] as $key => $value) {
                    if (is_array($value)) {
                        $preparedData[$key] = $value;
                    } else {
                        $preparedData[$dataSource][$key] = $value;
                    }
                }
                $this->renderContext->getStorage()->addData($this->getData('name'), $preparedData);
                $this->renderContext->getStorage()->addMeta($this->getData('name'), $preparedMeta);


//                if (isset($formLayout['configuration']['areas'][$name])) {
//                    $containerConfiguration = $formLayout['configuration']['areas'][$name];
//                }
//                $this->elements[] = $this->prepareFieldset($meta, $data[0], $containerConfiguration);
            }
        }
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
        if (isset($fieldData['dataType'])) {
            $type = $fieldData['dataType'];
        } else {
            if (isset($fieldData['frontend_input'])) {
                $type = $fieldData['frontend_input'];
            }
        }

        return $type;
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
        $formLayout = $this->getData('layout');
        $containerConfiguration = $formLayout['configuration'];

        if ($this->hasData('data_sources')) {
            foreach ($this->getData('data_sources') as $name => $dataSource) {
                $id = $this->renderContext->getRequestParam('id');
                $data = $id ? $this->dataManager->getDataById($dataSource, ['entity_id' => $id]) : [];
                $meta = $this->dataManager->getMetadata($dataSource);
                if (isset($formLayout['configuration']['areas'][$name])) {
                    $containerConfiguration = $formLayout['configuration']['areas'][$name];
                }
                $this->elements[] = $this->prepareFieldset($meta, $data[0], $containerConfiguration);
            }
        }
    }

    protected function prepareFieldset($meta, $data, $containerConfiguration)
    {
        $containerType = $containerConfiguration['type'];
        $elements = [];
        foreach ($meta as $metaKey => $metaData) {
            if ($metaKey == 'childDataSources') {
                foreach ($metaData as $key => $metaProvider) {
                    if (isset($formLayout['configuration']['areas'][$key])) {
                        $containerConfiguration = $containerConfiguration['configuration']['areas'][$key];
                    }
                    $elements[] = $this->prepareFieldset($metaProvider, $data[$key], $containerConfiguration);
                }
            } else {
                $metaData['value'] = isset($data[$metaData['name']]) ? $data[$metaData['name']] : null;
                $element = $this->renderContext->getRender()->getUiElementView($this->getFormElement($metaData));
                try {
                    $elements[] = $this->elementRendererBuilder->create($element, $metaData);
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
        $data = array_merge_recursive($containerConfiguration, ['elements' => $elements]);
        $container = $this->factory->create($containerType, $data);
        $container->prepare();
        return $container;
    }

    public function getContainer($name)
    {
        $containers = $this->getData('containers');
        if (!isset($containers[$name])) {
            throw new \Exception('Container ' . $name . ' does not exist!');
        }
        if (!$this->hasData('data_provider_pool')) {
            throw new \Exception('data_provider_pool is not specified!');
        }
        $dataProvider = $this->getRenderContext()->getStorage()->getDataProvider($name);
        $data = $dataProvider->getData();
        $elements = [];
        foreach ($dataProvider->getMeta() as $metaData) {
            $index = $this->getFieldIndex($metaData);
            $metaData['value'] = isset($data[$index]) ? $data[$index] : null;
            try {
                $elements[] = $this->factory->create($this->getFieldType($metaData), $metaData);
            } catch (\Exception $e) {
                continue;
            }
        }
        $data = array_merge_recursive($containers[$name], ['elements' => $elements]);
        /** @var \Magento\Ui\Component\Form\Fieldset $container */
        $container = $this->factory->create(
            $containers[$name]['name'],
            $data
        );
        $container->prepare();
        return $container;
    }

    /**
     * Get form element name
     *
     * @param array $metaField
     * @return string
     */
    protected function getFormElement(array $metaField)
    {
        if (isset($metaField['formElement'])) {
            return $metaField['formElement'];
        }
        if (!isset($metaField['dataType'])) {
            return static::DEFAULT_FORM_ELEMENT;
        }
        return isset($this->formElementMap[$metaField['dataType']])
            ? $this->formElementMap[$metaField['dataType']]
            : static::DEFAULT_FORM_ELEMENT;
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
