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
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools extends Mage_Backend_Block_Template
{
    /**
     * Alias of tab handle block in layout
     */
    const TAB_HANDLE_BLOCK_ALIAS = 'tab_handle';

    /**
     * @var Mage_DesignEditor_Model_Theme_Context
     */
    protected $_themeContext;

    /**
     * Initialize dependencies
     *
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_DesignEditor_Model_Theme_Context $themeContext
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_DesignEditor_Model_Theme_Context $themeContext,
        array $data = array()
    ) {
        $this->_themeContext = $themeContext;
        parent::__construct($context, $data);
    }

    /**
     * Get tabs data
     *
     * @return array
     */
    public function getTabs()
    {
        return array(
            array(
                'is_hidden'     => false,
                'is_disabled'   => false,
                'id'            => 'vde-tab-quick-styles',
                'label'         => __('Quick Styles'),
                'content_block' => 'design_editor_tools_quick-styles',
                'class'         => 'item-design'
            ),
            array(
                'is_hidden'     => true,
                'is_disabled'   => false,
                'id'            => 'vde-tab-block',
                'label'         => __('Block'),
                'content_block' => 'design_editor_tools_block',
                'class'         => 'item-block'
            ),
            array(
                'is_hidden'     => true,
                'is_disabled'   => false,
                'id'            => 'vde-tab-settings',
                'label'         => __('Settings'),
                'content_block' => 'design_editor_tools_settings',
                'class'         => 'item-settings'
            ),
            array(
                'is_hidden'     => false,
                'is_disabled'   => false,
                'id'            => 'vde-tab-code',
                'label'         => __('Advanced'),
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
     * Get save url
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/saveQuickStyles',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId())
        );
    }
}
