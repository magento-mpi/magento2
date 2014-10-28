<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Layout;

use Magento\Framework\View\Element\UiElementFactory;
use Magento\Ui\Component\AbstractView;
use Magento\Framework\View\Element\Template;
use Magento\Ui\DataProvider\Metadata;
use Magento\Ui\DataProvider\Manager;
use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\ConfigFactory;
use Magento\Framework\View\Element\UiComponent\ConfigBuilderInterface;
use Magento\Ui\DataProvider\Factory as DataProviderFactory;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * Class AbstractStructure
 */
class AbstractStructure extends AbstractView
{
    /**
     * @var array
     */
    protected $structure = [
        'sections' => [],
        'areas' => [],
        'groups' => [],
        'elements' => []
    ];

    /**
     * @var UiElementFactory
     */
    protected $factory;

    /**
     * Layout Namespace
     *
     * @var string
     */
    protected $ns;

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
        UiElementFactory $factory,
        array $data = []
    ) {
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
     * @inheritdoc
     */
    public function prepare()
    {
        $this->ns = $this->getData('name');

        $this->structure['form'] = [
            'type' => 'form',
            'children' => []
        ];

        $this->initSections();
        $this->initAreas();
        $this->initGroups();
        $this->initElements();

        foreach ($this->getDataSources() as $name => $dataSource) {
            $this->processDataSource($dataSource);
        }

        $this->processChildBLocks();

        $this->renderContext->getStorage()->addLayoutStructure(
            $this->getDataScope(),
            [
                'children' => $this->structure
            ]
        );

        if ($this->getData('configuration/tabs_container_name')) {
            $navBlock = $this->factory->create(
                \Magento\Ui\Component\Layout\Tabs\Nav::NAME,
                [
                    'data_scope' => $this->ns
                ]
            );
            $this->getRenderContext()->getPageLayout()
                ->addBlock($navBlock, 'tabs_nav', $this->getData('configuration/tabs_container_name'));
        }
    }

    /**
     * @return string
     */
    public function getDataScope()
    {
        return $this->ns;
    }

    /**
     * Prepare initial structure for sections
     *
     * @return void
     */
    protected function initSections()
    {
        $this->structure['sections'] = [
            'type' => \Magento\Ui\Component\Layout\Tabs\Nav::NAME,
            'config' => [
                'label' => $this->getData('label')
            ],
            'children' => []
        ];
    }

    /**
     * Prepare initial structure for areas
     *
     * @return void
     */
    protected function initAreas()
    {
        $this->structure['areas'] = [
            'type' => $this->ns,
            'children' => []
        ];
    }

    /**
     * Prepare initial structure for groups
     *
     * @return void
     */
    protected function initGroups()
    {
        $this->structure['groups'] = [
            'type' => $this->ns,
            'children' => []
        ];
    }

    /**
     * Prepare initial structure for elements
     *
     * @return void
     */
    protected function initElements()
    {
        $this->structure['elements'] = [
            'type' => $this->ns,
            'children' => []
        ];
    }

    /**
     * Get registered Data Sources
     *
     * @return array
     */
    protected function getDataSources()
    {
        return $this->getData('data_sources');
    }

    /**
     * @param string $dataSource
     * @return void
     */
    protected function processDataSource($dataSource)
    {
        $id = $this->renderContext->getRequestParam('id');

        $meta = $this->dataManager->getMetadata($dataSource);

        $referenceAreaName = $this->addArea(
            $dataSource,
            [
                'label' => $meta->getLabel()
            ]
        );
        $referenceGroupName = $this->addGroup(
            $dataSource,
            [
                'label' => $meta->getLabel()
            ]
        );

        foreach ($meta as $key => $value) {
            if (isset($value['visible']) && $value['visible'] === 'false') {
                continue;
            }
            if ($key != Metadata::CHILD_DATA_SOURCES) {
                $value['dataScope'] = $dataSource . '.' . $key;
                $referenceElementName = $this->addElement($key, $value);
                $this->addToGroup($dataSource, $referenceElementName);
            }
        }

        $this->addToArea($dataSource, $referenceGroupName);
        $this->addToSection($referenceAreaName);

        $children = $meta->get(Metadata::CHILD_DATA_SOURCES);
        foreach ($children as $childName) {
            $this->processChildDataSource($dataSource, $childName);
        }

        $preparedData = [
            $dataSource => []
        ];
        $data = $id ? $this->dataManager->getData($dataSource, ['entity_id' => $id]) : [];
        if ($data) {
            foreach (array_shift($data) as $key => $value) {
                $preparedData[$dataSource][$key] = $value;
            }
        }
        $this->renderContext->getStorage()->addData($this->getData('name'), $preparedData);
    }

    /**
     * @param string $dataSource
     * @param string $childName
     */
    protected function processChildDataSource($dataSource, $childName)
    {
        $childMeta = $this->dataManager->getMetadata($childName);

        $referenceChildAreaName = $this->addArea(
            $childName,
            [
                'label' => $childMeta->getLabel()
            ]
        );
        $this->addToSection($referenceChildAreaName);
        $referenceChildGroupName = $this->addGroup(
            $childName,
            [
                'label' => $childMeta->getLabel()
            ]
        );
        $this->addToArea($childName, $referenceChildGroupName);

        $itemTemplate = [
            'type' => 'template',
            'component' => 'Magento_Ui/js/form/components/collection/item',
            'childType' => 'group',
            'config' => [
                'label' => [
                    'default' => __('New ' . $childMeta->getLabel())
                ]
            ]
        ];
        if ($previewElements = $childMeta->getPreviewElements()) {
            $itemTemplate['config']['previewElements'] = explode(',', $previewElements);
        }
        if ($compositeLabel = $childMeta->getCompositeLabel()) {
            $itemTemplate['config']['label']['compositeOf'] = explode(',', $compositeLabel);
        }
        foreach ($childMeta as $key => $value) {
            $itemTemplate['children'][$key] = $value;
            $value['dataScope'] = $dataSource . '.' . $childName . '.' . $key;
            $itemTemplate['children'][$key]['config'] = $value;
            $itemTemplate['children'][$key]['type'] = 'group';
            $itemTemplate['children'][$key]['children'][$key] = [
                'type' => $value['formElement'],
                'config' => $value
            ];
        }

        $referenceCollectionName = $this->addCollection(
            $childName . 'Collection',
            [
                'active' => 1,
                'label' => $childMeta->getLabel(),
                'removeLabel' => __('Remove ' . $childMeta->getLabel()),
                'removeMessage' => __('Are you sure you want to delete this item?'),
                'addLabel' => __('Add New ' . $childMeta->getLabel()),
                'itemTemplate' => 'item_template',
                'dataScope' => "{$dataSource}.{$childName}"
            ]
        );
        $this->addTemplateToCollection($childName . 'Collection', 'item_template', $itemTemplate);

        $this->addToGroup($childName, $referenceCollectionName);
    }

    /**
     * @throws \Exception
     */
    protected function processChildBlocks()
    {
        //Add child blocks content
        foreach ($this->getData('child_blocks') as $blockName => $childBlock) {
            /** @var TabInterface $childBlock */
            if (!($childBlock instanceof TabInterface)) {
                throw new \Exception(__('"%s" tab should implement TabInterface', $blockName));
            }
            if (!$childBlock->canShowTab()) {
                continue;
            }
            $referenceAreaName = $this->addArea($blockName, ['label' => $childBlock->getTabTitle()]);
            $this->addToSection($referenceAreaName);
            $config = [
                'label' => $childBlock->getTabTitle()
            ];
            if ($childBlock->isAjaxLoaded()) {
                $config['source'] = $childBlock->getTabUrl();
            } else {
                $config['content'] = $childBlock->toHtml();
            }
            $referenceGroupName = $this->addGroup($blockName, $config, 'html_content');
            $this->addToArea($blockName, $referenceGroupName);
        }
    }

    /**
     * @param string $itemName
     */
    protected function addToSection($itemName)
    {
        $this->structure['sections']['children'][] = $itemName;
    }

    /**
     * @param string $areaName
     * @param array $config
     * @return string
     */
    protected function addArea($areaName, array $config = [])
    {
        $this->structure['areas']['children'][$areaName] = [
            'type' => 'tab',
            'config' => $config
        ];
        return "{$this->ns}.areas.{$areaName}";
    }

    /**
     * @param string $areaName
     * @param string $itemName
     */
    protected function addToArea($areaName, $itemName)
    {
        $this->structure['areas']['children'][$areaName]['children'][] = $itemName;
    }

    /**
     * @param string $groupName
     * @param array $config
     * @param string $type
     * @return string
     */
    protected function addGroup($groupName, array $config = [], $type = 'fieldset')
    {
        $this->structure['groups']['children'][$groupName] = [
            'type' => $type,
            'config' => $config
        ];
        return "{$this->ns}.groups.{$groupName}";
    }

    /**
     * @param string $groupName
     * @param string $itemName
     */
    protected function addToGroup($groupName, $itemName)
    {
        $this->structure['groups']['children'][$groupName]['children'][] = $itemName;
    }

    /**
     * @param string $elementName
     * @param array $config
     * @return string
     */
    protected function addElement($elementName, array $config = [])
    {
        $this->structure['elements']['children'][$elementName]['type'] = 'group';
        $this->structure['elements']['children'][$elementName]['children'][] = [
            'type' => $config['formElement'],
            'config' => $config
        ];
        return "{$this->ns}.elements.{$elementName}";
    }

    /**
     * @param string $collectionName
     * @param array $config
     * @return string
     */
    protected function addCollection($collectionName, array $config = [])
    {
        $this->structure['groups']['children'][$collectionName] = [
            'type' => 'collection',
            'config' => $config
        ];
        return "{$this->ns}.groups.{$collectionName}";
    }

    /**
     * @param string $collectionName
     * @param string $templateName
     * @param array $template
     */
    protected function addTemplateToCollection($collectionName, $templateName, $template)
    {
        $this->structure['groups']['children'][$collectionName]['children'][$templateName] = $template;
    }

    /**
     * Set tabs
     *
     * @param array $items
     * @return void
     */
    protected function sortTabs(array $items)
    {
        usort($items, [$this, 'compareSortOrder']);
    }

    /**
     * Compare sort order
     *
     * @param array $one
     * @param array $two
     * @return int
     */
    protected function compareSortOrder(array $one, array $two)
    {
        if (!isset($one['sort_order'])) {
            $one['sort_order'] = 0;
        }
        if (!isset($two['sort_order'])) {
            $two['sort_order'] = 0;
        }
        return (int)$one['sort_order'] - (int)$two['sort_order'];
    }
}
