<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Unassigned theme list
 */
class Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Unassigned
    extends Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
{
    /**
     * Get list title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Customized themes not assigned to a storeview');
    }

    /**
     * Get remove button
     *
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return string
     */
    protected function _addRemoveButtonHtml($themeBlock)
    {
        $themeId = $themeBlock->getTheme()->getId();
        /** @var $removeButton Mage_Backend_Block_Widget_Button */
        $removeButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');

        $removeButton->setData(array(
            'label'     => $this->__('Remove'),
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array(
                        'event' => 'delete',
                        'target' => 'body',
                        'eventData' => array(
                            'url' => $this->getUrl(
                                '*/system_design_theme/delete/',
                                array('id' => $themeId, 'back' => true)
                            )
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
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
     */
    protected function _addThemeButtons($themeBlock)
    {
        parent::_addThemeButtons($themeBlock);

        $this->_addDuplicateButtonHtml($themeBlock)->_addPreviewButtonHtml($themeBlock)
            ->_addAssignButtonHtml($themeBlock)->_addEditButtonHtml($themeBlock)->_addRemoveButtonHtml($themeBlock);
        return $this;
    }
}
