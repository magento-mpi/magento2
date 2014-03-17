<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser;

/**
 * A chooser for container for widget instances
 *
 * @method getTheme()
 * @method getArea()
 * @method \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Container setTheme($theme)
 * @method \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Container setArea($area)
 */
class Container extends \Magento\View\Element\Html\Select
{
    /**
     * @var \Magento\View\Layout\ProcessorFactory
     */
    protected $_layoutProcessorFactory;

    /**
     * @var \Magento\Core\Model\Resource\Theme\CollectionFactory
     */
    protected $_themesFactory;

    /**
     * @param \Magento\View\Element\Context $context
     * @param \Magento\View\Layout\ProcessorFactory $layoutProcessorFactory
     * @param \Magento\Core\Model\Resource\Theme\CollectionFactory $themesFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Context $context,
        \Magento\View\Layout\ProcessorFactory $layoutProcessorFactory,
        \Magento\Core\Model\Resource\Theme\CollectionFactory $themesFactory,
        array $data = array()
    ) {
        $this->_layoutProcessorFactory = $layoutProcessorFactory;
        $this->_themesFactory = $themesFactory;
        parent::__construct($context, $data);
    }

    /**
     * Assign attributes for the HTML select element
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setName('block');
        $this->setClass('required-entry select');
        $this->setExtraParams(
            'onchange="WidgetInstance.loadSelectBoxByType(\'block_template\',' .
            ' this.up(\'div.group_container\'), this.value)"'
        );
    }

    /**
     * Add necessary options
     *
     * @return \Magento\View\Element\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        if (!$this->getOptions()) {
            $layoutMergeParams = array('theme' => $this->_getThemeInstance($this->getTheme()));
            /** @var $layoutProcessor \Magento\View\Layout\ProcessorInterface */
            $layoutProcessor = $this->_layoutProcessorFactory->create($layoutMergeParams);
            $layoutProcessor->addPageHandles(array($this->getLayoutHandle()));
            $layoutProcessor->addPageHandles(array('default'));
            $layoutProcessor->load();

            $containers = $layoutProcessor->getContainers();
            if ($this->getAllowedContainers()) {
                foreach (array_keys($containers) as $containerName) {
                    if (!in_array($containerName, $this->getAllowedContainers())) {
                        unset($containers[$containerName]);
                    }
                }
            }
            asort($containers, SORT_STRING);

            $this->addOption('', __('-- Please Select --'));
            foreach ($containers as $containerName => $containerLabel) {
                $this->addOption($containerName, $containerLabel);
            }
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve theme instance by its identifier
     *
     * @param int $themeId
     * @return \Magento\Core\Model\Theme|null
     */
    protected function _getThemeInstance($themeId)
    {
        /** @var \Magento\Core\Model\Resource\Theme\Collection $themeCollection */
        $themeCollection = $this->_themesFactory->create();
        return $themeCollection->getItemById($themeId);
    }
}
