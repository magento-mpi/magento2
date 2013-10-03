<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Available theme list
 *
 * @method int getNextPage()
 * @method \Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList\Available setNextPage(int $page)
 */
namespace Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList;

class Available
    extends \Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList\AbstractSelectorList
{
    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Available Themes');
    }

    /**
     * Get next page url
     *
     * @return string
     */
    public function getNextPageUrl()
    {
        return $this->getNextPage() <= $this->getCollection()->getLastPageNumber()
            ? $this->getUrl('*/*/*', array('page' => $this->getNextPage()))
            : '';
    }

    /**
     * Get edit button
     *
     * @param \Magento\DesignEditor\Block\Adminhtml\Theme $themeBlock
     * @return $this
     */
    protected function _addEditButtonHtml($themeBlock)
    {
        $themeId = $themeBlock->getTheme()->getId();

        /** @var $assignButton \Magento\Backend\Block\Widget\Button */
        $assignButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button');
        $assignButton->setData(array(
            'label' => __('Edit'),
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array(
                        'event' => 'themeEdit',
                        'target' => 'body',
                        'eventData' => array(
                            'theme_id' => $themeId
                        )
                    ),
                ),
            ),
            'class' => 'action-edit',
        ));

        $themeBlock->addButton($assignButton);
    }

    /**
     * Add theme buttons
     *
     * @param \Magento\DesignEditor\Block\Adminhtml\Theme $themeBlock
     * @return \Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList\AbstractSelectorList
     */
    protected function _addThemeButtons($themeBlock)
    {
        parent::_addThemeButtons($themeBlock);
        $this->_addAssignButtonHtml($themeBlock);
        $this->_addEditButtonHtml($themeBlock);
        return $this;
    }
}
