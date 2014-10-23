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
 * Class Tabs
 */
class Tabs extends AbstractView
{
    /**
     * Other tabs key
     */
    const OTHER_TABS_KEY = 'other_tabs';

    /**
     * Tabs
     *
     * Structure:
     *
     * 'unique-key-1' => [
     *     'label' => 'some label',
     *     ...
     *     'content' => 'some content',
     *     'sort_order' => 1
     * ]
     *
     * @var array
     */
    protected $tabs = [];

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

    public function prepare()
    {
        $this->registerComponents();
        $ns = $this->getData('name');
        $tabs = [
            'type' => $ns,
            'children' => [
                $ns => [
                    'type' => 'nav',
                    'config' => [
                        'label' => $this->getData('label')
                    ]
                ]
            ]
        ];
        $areas = [
            'type' => $ns,
            'children' => [
                $ns => [
                    'type' => $ns
                ]
            ]
        ];
        $fields = [
            'childType' => 'group',
            'children' => [
                $ns => []
            ]
        ];
        $fieldSets = [
            'children' => [
                $ns => [
                    'childType' => 'fieldset',
                ]
            ]
        ];
        $collections = [
            'children' => [
                $ns => []
            ]
        ];
        //Add child blocks content
        foreach ($this->getData('childBlocks') as $childBlock) {
            /** @var TabInterface $childBlock */
            if (!($childBlock instanceof TabInterface)) {
                throw new \Exception($childBlock->getNameInLayout() . 'should implement TabInterface');
            }
            $tabs['children'][$ns]['children'][] = 'areas.' . $ns . '.' . $childBlock->getNameInLayout();
            $areas['children'][$ns]['children'][$childBlock->getNameInLayout()] = [
                'type' => 'tab',
                'config' => [
                    'name' => $childBlock->getNameInLayout(),
                    'label' => $childBlock->getTabTitle()
                ],
                'children' => ['fieldSets.' . $ns . '.additional_tabs.' . $childBlock->getNameInLayout()]
            ];
            if ($childBlock->isAjaxLoaded()) {
                $fieldSets['children'][$ns]['children']['additional_tabs']['children'][$childBlock->getNameInLayout()] = [
                    'type' => 'html_content',
                    'config' => [
                        'source' => $childBlock->getTabUrl()
                    ]
                ];
            } else {
                $fieldSets['children'][$ns]['children']['additional_tabs']['children'][$childBlock->getNameInLayout()] = [
                    'type' => 'html_content',
                    'config' => [
                        'content' => $childBlock->toHtml()
                    ]
                ];
            }
        }

        $id = $this->renderContext->getRequestParam('id');
        foreach ($this->getData('dataSources') as $name => $dataSource) {
            $tabs['children'][$ns]['children'][] = 'areas.' . $ns . '.' . $dataSource;
            $meta = $this->dataManager->getMetadata($dataSource);
            $fieldSets['children'][$ns]['children'][$dataSource] = [
                'type' => 'fieldset',
                'config' => [
                    'label' => $dataSource,
                    'collapsible' => true
                ]
            ];

            $fields['children'][$ns]['component'] = 'Magento_Ui/js/form';

            foreach ($meta as $key => $value) {
                if ($key != Metadata::CHILD_DATA_SOURCES) {
                    $fieldSets['children'][$ns]['children'][$dataSource]['children'][] = 'fields.' . $ns . '.' . $key;
                    $fields['children'][$ns]['children'][$key] = $value;
                    $fields['children'][$ns]['children'][$key]['type'] = 'group';
                    $value['dataScope'] = $dataSource . '.' . $key;
                    $fields['children'][$ns]['children'][$key]['children'][$key] = [
                        'type' => $value['formElement'],
                        'config' => $value
                    ];
                }
            }

            $areas['children'][$ns]['children'][$dataSource] = [
                'type' => 'tab',
                'config' => [
                    'active' => true,
                    'name' => $dataSource,
                    'label' => $meta->getLabel()
                ],
                'children' => ['fieldSets.' . $ns . '.' . $dataSource]
            ];

            $children = $meta->get(Metadata::CHILD_DATA_SOURCES);
            foreach ($children as $childName) {
                $tabs['children'][$ns]['children'][] = 'areas.' . $ns . '.' . $childName;
                $childMeta = $this->dataManager->getMetadata($childName);
                $fieldSets['children'][$ns]['children'][$childName] = [
                    'type' => 'fieldset',
                    'config' => [
                        'label' => $childMeta->getLabel()
                    ],
                    'children' => ['fields.' . $ns . '.' . $childName]
                ];

                $collection = [
                    'type' => 'collection',
                    'parentName' => 'fields.' . $ns,
                    'config' => [
                        'active' => 1,
                        'label' => $childMeta->getLabel(),
                        'removeLabel' => __('Remove ' . $childMeta->getLabel()),
                        'removeMessage' => __('Are you sure you want to delete this item?'),
                        'addLabel' => __('Add New ' . $childMeta->getLabel()),
                        'itemTemplate' => 'fields.' . $ns . '.' . $childName . '.item_template'
                    ]
                ];

                $itemTemplate = [
                    'type' => 'template',
                    'component' => 'Magento_Ui/js/form/components/collection/item',
                    'childType' => 'group',
                    'parentName' => 'fields.' . $ns . '.' . $childName,
                    'config' => [
                        'title' => [
                            'default' => 'New Customer Address',
                            'composite_of' => ['prefix', 'firstname', 'lastname']
                        ]
                    ]
                ];
                foreach ($childMeta as $key => $value) {
                    $itemTemplate['children'][$key] = $value;
                    $value['dataScope'] = $dataSource . '.' . $childName . '.' . $key;
                    $itemTemplate['children'][$key] = [
                        'type' => 'group',
                        'children' => [
                            $key => [
                                'type' => $value['formElement'],
                                'config' => $value
                            ]
                        ]
                    ];
                }
                $collection['children']['item_template'] = $itemTemplate;
                $areas['children'][$ns]['children'][$childName] = [
                    'type' => 'tab',
                    'config' => [
                        'label' => $childMeta->getLabel()
                    ],
                    'children' => ['fieldSets.' . $ns . '.' . $childName]
                ];
                //$collections['children'][$ns]['children'][$childName] = $collection;
                $this->renderContext->getStorage()->addLayoutNode($childName, $collection);
            }

            $this->renderContext->getStorage()->addLayoutNode('tabs', $tabs);
            $this->renderContext->getStorage()->addLayoutNode('areas', $areas);
            $this->renderContext->getStorage()->addLayoutNode('fieldSets', $fieldSets);
            $this->renderContext->getStorage()->addLayoutNode('fields', $fields);

            $preparedData = [];
            $data = $id ? $this->dataManager->getData($dataSource, ['entity_id' => $id]) : [];
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $preparedData[$key] = $value;
                } else {
                    $preparedData[$dataSource][$key] = $value;
                }
            }
            $this->renderContext->getStorage()->addData($this->getData('name'), $preparedData);
        }

        if ($this->getData('configuration/tabs_container_name')) {
            $navBlock = $this->factory->create('nav', [
                    'data_scope' => $ns
                ]);
            $this->getRenderContext()->getPageLayout()
                ->addBlock($navBlock, 'tabs_nav', $this->getData('configuration/tabs_container_name'));
        }
    }

    public function registerComponents()
    {
        foreach ($this->getLayout()->getAllBlocks() as $block) {
            if ($block instanceof \Magento\Framework\View\Element\UiComponentInterface) {
                $config = (array)$block->getData('js_config');
                if (!isset($config['extends'])) {
                    $config['extends'] = $this->getData('name');
                }
                $this->renderContext->getStorage()->addComponent($block->getNameInLayout(), $config);
            }
        };
    }

    /**
     * Compare sort order
     *
     * @param array $tabOne
     * @param array $tabTwo
     * @return int
     */
    public function compareSortOrder(array $tabOne, array $tabTwo)
    {
        if (!isset($tabOne['sort_order'])) {
            $tabOne['sort_order'] = 0;
        }
        if (!isset($tabTwo['sort_order'])) {
            $tabTwo['sort_order'] = 0;
        }

        return (int)$tabOne['sort_order'] - (int)$tabTwo['sort_order'];
    }

    /**
     * Get tabs
     *
     * @return array
     */
    public function getTabs()
    {
        $tabs = $this->renderContext->getStorage()->getLayoutNode('tabs');
        return isset($tabs['children']) ? $tabs['children'] : [];
    }

    /**
     * Set tabs
     *
     * @param array $tabs
     * @return void
     */
    protected function setTabs(array $tabs)
    {
        if (!empty($tabs)) {
            $this->tabs = array_merge($this->tabs, $tabs);
            usort($this->tabs, [$this, 'compareSortOrder']);
        }
    }

    /**
     * Get configured tabs
     *
     * @return array
     */
    protected function getConfiguredTabs()
    {
        $tabs = [];
        $tabsConfig = $this->hasData(static::OTHER_TABS_KEY) ? $this->getData('other_tabs') : [];
        foreach ($tabsConfig as $name => $tab) {
            $block = $this->getLayout()->getBlock($name);
            if ($block !== false) {
                $tab['elements'] = [$block];
                $tabs[$name] = $tab;
            }
        }

        return $tabs;
    }
}
