<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

// @codingStandardsIgnoreStart
/**
 * Abstract theme list
 *
 * @method Mage_Core_Model_Resource_Theme_Collection getCollection()
 * @method bool|null getIsFirstEntrance()
 * @method bool|null getHasThemeAssigned()
 * @method Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract setHasThemeAssigned(bool $flag)
 * @method Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract|Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Available setCollection(Mage_Core_Model_Resource_Theme_Collection $collection)
 * @method Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract setIsFirstEntrance(bool $flag)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
// @codingStandardsIgnoreEnd
abstract class Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
    extends Mage_Core_Block_Template
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
        if (!empty($themeCollection)) {
            /** @var $theme Mage_Core_Model_Theme */
            foreach ($themeCollection as $theme) {
                $itemBlock->setTheme($theme);
                $this->_addThemeButtons($itemBlock);
                $items[] = $this->getChildHtml('theme', false);
            }
        }
        return $items;
    }

    /**
     * Add duplicate button
     *
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return $this
     */
    protected function _addDuplicateButtonHtml($themeBlock)
    {
        $themeId = $themeBlock->getTheme()->getId();

        /** @var $assignButton Mage_Backend_Block_Widget_Button */
        $assignButton = $this->getLayout()->createBlock('Mage_DesignEditor_Block_Adminhtml_Theme_Button');
        $assignButton->setData(array(
            'title' => $this->__('Duplicate'),
            'label' => $this->__('Duplicate'),
            'class'   => 'action-duplicate',
            'href'   => $this->getUrl('*/*/duplicate', array('theme_id' => $themeId))
        ));

        $themeBlock->addButton($assignButton);
        return $this;
    }

    /**
     * Get assign to store-view button
     *
     * This button used on "Available Themes" tab and "My Customizations" tab
     *
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return $this
     */
    protected function _addAssignButtonHtml($themeBlock)
    {
        $title = $this->__('Assign New Theme');
        if ($this->getHasThemeAssigned()) {
            // @codingStandardsIgnoreStart
            $message = $this->__('You chose a new theme for your live store. Click "Ok" to replace your current theme.');
            // @codingStandardsIgnoreEnd
        } else {
            // @codingStandardsIgnoreStart
            $message = $this->__('You chose a theme for your new store. Click "Ok" to go live. You can always modify or switch themes in "My Customizations" and "Available Themes."');
            // @codingStandardsIgnoreEnd
        }
        $themeId = $themeBlock->getTheme()->getId();

        /** @var $assignButton Mage_Backend_Block_Widget_Button */
        $assignButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');
        $assignButton->setData(array(
            'label'   => $this->__('Assign to a Store View'),
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array(
                        'event'     => 'assign',
                        'target'    => 'body',
                        'eventData' => array(
                            'theme_id' => $themeId,
                            'confirm'  => array(
                                'message' =>  $message,
                                'title'   =>  $title
                            )
                        )
                    ),
                ),
            ),
            'class'   => 'save action-theme-assign primary',
        ));

        $themeBlock->addButton($assignButton);
        return $this;
    }

    /**
     * Get edit button
     *
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return $this
     */
    protected function _addEditButtonHtml($themeBlock)
    {
        /** @var $editButton Mage_Backend_Block_Widget_Button */
        $editButton = $this->getLayout()->createBlock('Mage_DesignEditor_Block_Adminhtml_Theme_Button');
        $editButton->setData(array(
            'title'  => $this->__('Edit'),
            'label'  => $this->__('Edit'),
            'class'  => 'action-edit',
            'href'   => $this->_getEditUrl($themeBlock->getTheme()->getId()),
            'target' => '_blank',
        ));

        $themeBlock->addButton($editButton);
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
            'mode'     => Mage_DesignEditor_Model_State::MODE_NAVIGATION
        ));
    }
}
