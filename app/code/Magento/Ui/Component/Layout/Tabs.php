<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Layout;

use Magento\Ui\Component\AbstractView;
use Magento\Framework\View\Element\Template;
use Magento\Ui\DataProvider\Metadata;

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
     * Prepare component data
     *
     * @return void
     */
//    public function prepare()
//    {
//        parent::prepare();
//        $configData = $this->getDefaultConfiguration();
//        if ($this->hasData('config')) {
//            $configData = array_merge($configData, $this->getData('config'));
//        }
//
//        $this->prepareConfiguration($configData);
//
//        $this->createDataProviders();
//    }

    public function prepare()
    {
        $this->registerComponents();
        foreach ($this->getData('dataSources') as $name => $dataSource) {
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
            foreach ($this->getData('childBlocks') as $childBlock) {
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
        }
    }

    public function registerComponents()
    {
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
        $this->setTabs($this->getConfiguredTabs());
        return $this->tabs;
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
