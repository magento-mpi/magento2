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
 * Abstract theme list
 *
 * @method Mage_Core_Model_Resource_Theme_Collection getCollection()
 * @method Mage_Backend_Block_Abstract setCollection(Mage_Core_Model_Resource_Theme_Collection $collection)
 */
abstract class Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
    extends Mage_Backend_Block_Abstract
{
    /**
     * Get tab title
     *
     * @return string
     */
    abstract public function getTabTitle();

    /**
     * Add theme buttons
     *
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
     */
    protected function _addThemeButtons($themeBlock)
    {
        $themeBlock->clearButtons();
        return $this;
    }

    /**
     * Get list items of themes
     *
     * @return array
     */
    public function getListItems()
    {
        /** @var $itemBlock Mage_DesignEditor_Block_Adminhtml_Theme */
        $itemBlock = $this->getChildBlock('theme');

        $themeCollection = $this->getCollection();

        $items = array();
        /** @var $theme Mage_Core_Model_Theme */
        foreach ($themeCollection as $theme) {
            $itemBlock->setTheme($theme);
            $this->_addThemeButtons($itemBlock);
            $items[] = $this->getChildHtml('theme', false);
        }

        return $items;
    }

    /**
     * Get assign to storeview button
     *
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return string
     */
    protected function _addAssignButtonHtml($themeBlock)
    {
        $themeId = $themeBlock->getTheme()->getId();
        /** @var $assignButton Mage_Backend_Block_Widget_Button */
        $assignButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');
        $assignButton->setData(array(
            'label'   => $this->__('Assign to a Storeview'),
            'data_attr'  => array(
                'widget-button' => array(
                    'event' => 'assign',
                    'related' => 'body',
                    'eventData' => array(
                        'theme_id' => $themeId
                    )
                ),
            ),
            'class'   => 'save action-theme-assign',
            'target'  => '_blank'
        ));

        $themeBlock->addButton($assignButton);
        return $this;
    }

    /**
     * Get preview button
     *
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return string
     */
    protected function _addPreviewButtonHtml($themeBlock)
    {
        /** @var $previewButton Mage_Backend_Block_Widget_Button */
        $previewButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');
        $previewButton->setData(array(
            'label'     => $this->__('Preview Theme'),
            'class'     => 'preview-default',
            'data_attr' => array(
                'widget-button' => array(
                    'event' => 'preview',
                    'related' => 'body',
                    'eventData' => array(
                        'preview_url' => $this->_getPreviewUrl($themeBlock->getTheme()->getId())
                    )
                ),
            )
        ));

        $themeBlock->addButton($previewButton);
        return $this;
    }

    /**
     * Get edit button
     *
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return string
     */
    protected function _addEditButtonHtml($themeBlock)
    {
        /** @var $previewButton Mage_Backend_Block_Widget_Button */
        $previewButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');
        $previewButton->setData(array(
            'label'     => $this->__('Edit Theme'),
            'class'     => 'add edit-theme',
            'data_attr' => array(
                'widget-button' => array(
                    'event' => 'preview',
                    'related' => 'body',
                    'eventData' => array(
                        'preview_url' => $this->_getEditUrl($themeBlock->getTheme()->getId())
                    )
                ),
            )
        ));

        $themeBlock->addButton($previewButton);
        return $this;
    }

    /**
     * Get preview url for selected theme
     *
     * @param int $themeId
     * @return string
     */
    protected function _getPreviewUrl($themeId)
    {
        return $this->getUrl('*/*/launch', array(
            'theme_id' => $themeId,
            'mode'     => Mage_DesignEditor_Model_State::MODE_NAVIGATION
        ));
    }

    /**
     * Get edit theme url for selected theme
     *
     * @param int $themeId
     * @return string
     */
    protected function _getEditUrl($themeId)
    {
        return $this->getUrl('*/*/launch', array(
            'theme_id' => $themeId,
            'mode'     => Mage_DesignEditor_Model_State::MODE_DESIGN
        ));
    }
}
