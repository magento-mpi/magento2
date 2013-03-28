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
 * Block that renders VDE tools panel
 *
 * @method string getMode()
 * @method Mage_DesignEditor_Block_Adminhtml_Editor_Tools setMode($mode)
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools extends Mage_Core_Block_Template
{
    /**
     * Alias of tab handle block in layout
     */
    const TAB_HANDLE_BLOCK_ALIAS = 'tab_handle';

    /**
     * Get tabs data
     *
     * @return array
     */
    public function getTabs()
    {
        $isDisabled = $this->getMode() == Mage_DesignEditor_Model_State::MODE_NAVIGATION;
        return array(
            array(
                'is_hidden'     => false,
                'is_disabled'   => $isDisabled,
                'id'            => 'vde-tab-quick-styles',
                'label'         => $this->__('Quick Styles'),
                'content_block' => 'design_editor_tools_quick-styles',
                'class'         => 'item-design'
            ),
            array(
                'is_hidden'     => true,
                'is_disabled'   => $isDisabled,
                'id'            => 'vde-tab-block',
                'label'         => $this->__('Block'),
                'content_block' => 'design_editor_tools_block',
                'class'         => 'item-block'
            ),
            array(
                'is_hidden'     => true,
                'is_disabled'   => $isDisabled,
                'id'            => 'vde-tab-settings',
                'label'         => $this->__('Settings'),
                'content_block' => 'design_editor_tools_settings',
                'class'         => 'item-settings'
            ),
            array(
                'is_hidden'     => false,
                'is_disabled'   => $isDisabled,
                'id'            => 'vde-tab-code',
                'label'         => $this->__('Scripts'),
                'content_block' => 'design_editor_tools_code',
                'class'         => 'item-code'
            ),
        );
    }

    /**
     * Get tabs html
     *
     * @return array
     */
    public function getTabContents()
    {
        $contents = array();
        foreach ($this->getTabs() as $tab) {
            $contents[] = $this->getChildHtml($tab['content_block']);
        }
        return $contents;
    }

    /**
     * Get tabs handles
     *
     * @return array
     */
    public function getTabHandles()
    {
        /** @var $tabHandleBlock Mage_Backend_Block_Template */
        $tabHandleBlock = $this->getChildBlock(self::TAB_HANDLE_BLOCK_ALIAS);
        $handles = array();
        foreach ($this->getTabs() as $tab) {
            $href = '#' . $tab['id'];
            $handles[] = $tabHandleBlock->setIsHidden($tab['is_hidden'])
                ->setIsDisabled($tab['is_disabled'])
                ->setHref($href)
                ->setClass($tab['class'])
                ->setTitle($tab['label'])
                ->setLabel($tab['label'])
                ->toHtml();
        }

        return $handles;
    }

    /**
     * Return theme identification number
     *
     * @return int|null
     */
    protected function getThemeId()
    {
        /** @var $helper Mage_DesignEditor_Helper_Data */
        $helper = $this->_helperFactory->get('Mage_DesignEditor_Helper_Data');
        return $helper->getEditableThemeId();
    }

    /**
     * Get save url
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/saveQuickStyles',
            array('theme_id' => $this->getThemeId())
        );
    }
}
