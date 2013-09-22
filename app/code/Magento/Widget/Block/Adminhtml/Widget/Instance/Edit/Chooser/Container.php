<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * A chooser for container for widget instances
 *
 * @method getTheme()
 * @method getArea()
 * @method \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Container setTheme($theme)
 * @method \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Container setArea($area)
 */
namespace Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser;

class Container extends \Magento\Core\Block\Html\Select
{
    /**
     * @var Magento_Core_Model_Layout_MergeFactory
     */
    protected $_layoutMergeFactory;

    /**
     * @var Magento_Core_Model_Resource_Theme_CollectionFactory
     */
    protected $_themeCollFactory;

    /**
     * @param Magento_Core_Model_Layout_MergeFactory $layoutMergeFactory
     * @param Magento_Core_Model_Resource_Theme_CollectionFactory $themeCollFactory
     * @param Magento_Core_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Layout_MergeFactory $layoutMergeFactory,
        Magento_Core_Model_Resource_Theme_CollectionFactory $themeCollFactory,
        Magento_Core_Block_Context $context,
        array $data = array()
    ) {
        $this->_layoutMergeFactory = $layoutMergeFactory;
        $this->_themeCollFactory = $themeCollFactory;
        parent::__construct($context, $data);
    }

    /**
     * Assign attributes for the HTML select element
     */
    protected function _construct()
    {
        $this->setName('block');
        $this->setClass('required-entry select');
        $this->setExtraParams('onchange="WidgetInstance.loadSelectBoxByType(\'block_template\','
            . ' this.up(\'div.group_container\'), this.value)"');
    }

    /**
     * Add necessary options
     *
     * @return \Magento\Core\Block\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        if (!$this->getOptions()) {
            $layoutMergeParams = array(
                'theme' => $this->_getThemeInstance($this->getTheme()),
            );
            /** @var $layoutMerge Magento_Core_Model_Layout_Merge */
            $layoutMerge = $this->_layoutMergeFactory->create($layoutMergeParams);
            $layoutMerge->addPageHandles(array($this->getLayoutHandle()));
            $layoutMerge->load();

            $containers = $layoutMerge->getContainers();
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
        /** @var Magento_Core_Model_Resource_Theme_Collection $themeCollection */
        $themeCollection = $this->_themeCollFactory->create();
        return $themeCollection->getItemById($themeId);
    }
}
