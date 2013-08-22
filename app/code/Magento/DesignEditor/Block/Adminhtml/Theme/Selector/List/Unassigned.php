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
 * Unassigned theme list
 */
class Magento_DesignEditor_Block_Adminhtml_Theme_Selector_List_Unassigned
    extends Magento_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
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
     * @param Magento_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return string
     */
    protected function _addRemoveButtonHtml($themeBlock)
    {
        $themeId = $themeBlock->getTheme()->getId();
        $themeTitle = $themeBlock->getTheme()->getThemeTitle();
        /** @var $removeButton Magento_Backend_Block_Widget_Button */
        $removeButton = $this->getLayout()->createBlock('Magento_Backend_Block_Widget_Button');

        $removeButton->setData(array(
            'label'     => __('Remove'),
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array(
                        'event' => 'delete',
                        'target' => 'body',
                        'eventData' => array(
                            'url' => $this->getUrl(
                                '*/system_design_theme/delete/',
                                array('id' => $themeId, 'back' => true)
                            ),
                            'confirm' => array(
                                'message' => __('Are you sure you want to delete this theme?'),
                            ),
                            'title' => __('Delete %1 Theme', $themeTitle)
                        )
                    ),
                ),
            ),
            'class'   => 'action-delete',
            'target'  => '_blank'
        ));

        $themeBlock->addButton($removeButton);
        return $this;
    }

    /**
     * Add theme buttons
     *
     * @param Magento_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return Magento_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
     */
    protected function _addThemeButtons($themeBlock)
    {
        parent::_addThemeButtons($themeBlock);

        $this->_addDuplicateButtonHtml($themeBlock)->_addAssignButtonHtml($themeBlock)->_addEditButtonHtml($themeBlock)
            ->_addRemoveButtonHtml($themeBlock);
        return $this;
    }
}
