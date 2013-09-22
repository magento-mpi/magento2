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
 * Widget Instance layouts chooser
 *
 * @method getArea()
 * @method getTheme()
 */
namespace Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser;

class Layout extends \Magento\Core\Block\Html\Select
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
     * Add necessary options
     *
     * @return \Magento\Core\Block\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        if (!$this->getOptions()) {
            $this->addOption('', __('-- Please Select --'));
            $layoutMergeParams = array(
                'theme' => $this->_getThemeInstance($this->getTheme()),
            );
            $pageTypes = array();
            $pageTypesAll = $this->_getLayoutMerge($layoutMergeParams)->getPageHandlesHierarchy();
            foreach ($pageTypesAll as $pageTypeName => $pageTypeInfo) {
                $layoutMerge = $this->_getLayoutMerge($layoutMergeParams);
                $layoutMerge->addPageHandles(array($pageTypeName));
                $layoutMerge->load();
                if (!$layoutMerge->getContainers()) {
                    continue;
                }
                $pageTypes[$pageTypeName] = $pageTypeInfo;
            }
            $this->_addPageTypeOptions($pageTypes);
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

    /**
     * Retrieve new layout merge model instance
     *
     * @param array $arguments
     * @return \Magento\Core\Model\Layout\Merge
     */
    protected function _getLayoutMerge(array $arguments)
    {
        return $this->_layoutMergeFactory->create($arguments);
    }

    /**
     * Add page types information to the options
     *
     * @param array $pageTypes
     * @param int $level
     */
    protected function _addPageTypeOptions(array $pageTypes, $level = 0)
    {
        foreach ($pageTypes as $pageTypeName => $pageTypeInfo) {
            $params = array();
            if ($pageTypeInfo['type'] == \Magento\Core\Model\Layout\Merge::TYPE_FRAGMENT) {
                $params['class'] = 'fragment';
            }
            $this->addOption($pageTypeName, str_repeat('. ', $level) . $pageTypeInfo['label'], $params);
            $this->_addPageTypeOptions($pageTypeInfo['children'], $level + 1);
        }
    }
}
