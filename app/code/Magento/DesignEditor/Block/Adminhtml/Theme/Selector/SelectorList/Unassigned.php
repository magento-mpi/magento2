<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Unassigned theme list
 */
namespace Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList;

class Unassigned extends \Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList\AbstractSelectorList
{
    /**
     * Get list title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Themes Not Assigned to Store Views');
    }

    /**
     * Get remove button
     *
     * @param \Magento\DesignEditor\Block\Adminhtml\Theme $themeBlock
     * @return string
     */
    protected function _addRemoveButtonHtml($themeBlock)
    {
        $themeId = $themeBlock->getTheme()->getId();
        $themeTitle = $themeBlock->getTheme()->getThemeTitle();
        /** @var $removeButton \Magento\Backend\Block\Widget\Button */
        $removeButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button');

        $removeButton->setData(
            array(
                'label' => __('Remove'),
                'data_attribute' => array(
                    'mage-init' => array(
                        'button' => array(
                            'event' => 'delete',
                            'target' => 'body',
                            'eventData' => array(
                                'url' => $this->getUrl(
                                    '*/system_design_theme/delete/',
                                    array('id' => $themeId, 'back' => true)
                                ),
                                'confirm' => array('message' => __('Are you sure you want to delete this theme?')),
                                'title' => __('Delete %1 Theme', $themeTitle)
                            )
                        )
                    )
                ),
                'class' => 'action-delete',
                'target' => '_blank'
            )
        );

        $themeBlock->addButton($removeButton);
        return $this;
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

        $this->_addDuplicateButtonHtml(
            $themeBlock
        )->_addAssignButtonHtml(
            $themeBlock
        )->_addEditButtonHtml(
            $themeBlock
        )->_addRemoveButtonHtml(
            $themeBlock
        );
        return $this;
    }
}
