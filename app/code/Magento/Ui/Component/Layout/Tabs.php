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
     * Flag if changed object
     *
     * @var bool
     */
    protected $isChanged = true;

    /**
     * Prepare component data
     *
     * @return $this|void
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
        $this->setOtherTabs();
    }

    /**
     * Set context component
     *
     * @param ContextBehaviorInterface $component
     * @return mixed
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
     * Get other tabs from config
     *
     * @return array
     */
    public function getOtherTabsConfig()
    {
        return $this->hasData(static::OTHER_TABS_KEY) ? $this->getData('other_tabs') : [];
    }

    /**
     * Set other blocks tabs
     *
     * @return void
     */
    protected function setOtherTabs()
    {
        $names = $this->getLayout()->getChildNames(static::OTHER_TABS_KEY);
        if (!empty($names)) {
            foreach ($names as $name) {
                $this->otherBlocks[$name] = $this->getLayout()->getBlock($name);
            }
        }
    }

    /**
     * Render other tab by name
     *
     * @param $tabName
     * @return string
     */
    public function renderOtherTabs($tabName)
    {
        return isset($this->otherBlocks[$tabName]) ? $this->otherBlocks[$tabName]->toHtml() : '';
    }
}
