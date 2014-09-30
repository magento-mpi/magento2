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
use Magento\Ui\Component\ContextBehaviorInterface;

/**
 * Class Tabs
 */
class Tabs extends AbstractView implements ContextBehaviorInterface
{
    /**
     * Other tabs key
     */
    const OTHER_TABS_KEY = 'other_tabs';

    /**
     * Other blocks tabs
     *
     * @var Template[]
     */
    protected $otherBlocks = [];

    /**
     * Context component
     *
     * @var ContextBehaviorInterface
     */
    protected $contextComponent;

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
    public function prepare()
    {
        parent::prepare();
        $configData = $this->getDefaultConfiguration();
        if ($this->hasData('config')) {
            $configData = array_merge($configData, $this->getData('config'));
        }

        $config = $this->configFactory->create(
            [
                'name' => $this->renderContext->getNamespace() . '_' . $this->getNameInLayout(),
                'parentName' => $this->renderContext->getNamespace(),
                'configuration' => $configData
            ]
        );
        $this->setConfig($config);
        $this->renderContext->getStorage()->addComponentsData($config);

        $this->createDataProviders();
    }

    /**
     * Set context component
     *
     * @param ContextBehaviorInterface $component
     * @return void
     */
    public function setContext(ContextBehaviorInterface $component)
    {
        $this->contextComponent = $component;
    }

    /**
     * Get context component
     *
     * @return ContextBehaviorInterface
     */
    public function getContext()
    {
        return isset($this->contextComponent) ? $this->contextComponent : $this;
    }

    /**
     * Shortcut for rendering as HTML
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->isContext() ? '' : $this->render();
    }

    /**
     * Is the object context
     *
     * @return bool
     */
    public function isContext()
    {
        return isset($this->contextComponent);
    }

    /**
     * Compare sort order
     *
     * @param array $tabOne
     * @param array $tabTwo
     * @return int
     */
    public function compareSortOrder(array $tabOne, array $tabTwo) {
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
                $tab['content'] = $block->toHtml();
                $tabs[$name] = $tab;
            }
        }

        return $tabs;
    }
}
