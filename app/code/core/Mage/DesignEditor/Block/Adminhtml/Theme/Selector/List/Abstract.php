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
            'label'     => $this->__('Assign to a Storeview'),
            'onclick'   => "alert('Assign to a Storeview id: $themeId')",
            'class'     => 'add',
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
        $themeId = $themeBlock->getTheme()->getId();
        /** @var $previewButton Mage_Backend_Block_Widget_Button */
        $previewButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');

        $previewButton->setData(array(
            'label'     => $this->__('Preview Theme'),
            'onclick'   => "alert('Preview Theme id: $themeId')",
            'class'     => 'add',
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
        $themeId = $themeBlock->getTheme()->getId();
        /** @var $editButton Mage_Backend_Block_Widget_Button */
        $editButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');

        $editButton->setData(array(
            'label'     => $this->__('Edit Button'),
            'onclick'   => "alert('Edit Button id: $themeId')",
            'class'     => 'add',
        ));

        $themeBlock->addButton($editButton);
        return $this;
    }
}
