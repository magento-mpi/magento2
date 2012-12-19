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
 * VDE buttons block
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons extends Mage_Backend_Block_Template
{
    /**
     * Current theme used for preview
     *
     * @var int
     */
    protected $_themeId;

    /**
     * Current VDE mode
     *
     * @var int
     */
    protected $_mode;

    /**
     * Get current theme id
     *
     * @return int
     */
    public function getThemeId()
    {
        return $this->_themeId;
    }

    /**
     * Get current theme id
     *
     * @param int $themeId
     * @return Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons
     */
    public function setThemeId($themeId)
    {
        $this->_themeId = $themeId;

        return $this;
    }

    /**
     * Get current VDE mode
     *
     * @return int
     */
    public function getMode()
    {
        return $this->_mode;
    }

    /**
     * Get current VDE mode
     *
     * @param int $mode
     * @return Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons
     */
    public function setMode($mode)
    {
        $this->_mode = $mode;

        return $this;
    }

    /**
     * Get "View Layout" button URL
     *
     * @return string
     */
    public function getViewLayoutUrl()
    {
        return $this->getUrl('*/*/getLayoutUpdate');
    }

    /**
     * Get "Back" button URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }

    /**
     * Get "Navigation Mode" button URL
     *
     * @return string
     */
    public function getNavigationModeUrl()
    {
        return $this->getUrl('*/*/launch', array(
            'mode' => Mage_DesignEditor_Model_State::MODE_NAVIGATION,
            'theme_id' => $this->getThemeId()
        ));
    }

    /**
     * Get "Design Mode" button URL
     *
     * @return string
     */
    public function getDesignModeUrl()
    {
        return $this->getUrl('*/*/launch', array(
            'mode' => Mage_DesignEditor_Model_State::MODE_DESIGN,
            'theme_id' => $this->getThemeId()
        ));
    }

    /**
     * Check if visual editor is in navigation mode
     *
     * @return bool
     */
    public function isNavigationMode()
    {
        return $this->getMode() == Mage_DesignEditor_Model_State::MODE_NAVIGATION;
    }

    /**
     * Get assign to storeview button
     *
     * @return string
     */
    public function getAssignButtonHtml()
    {
        /** @var $assignButton Mage_Backend_Block_Widget_Button */
        $assignButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');
        $assignButton->setData(array(
            'label'   => $this->__('Assign this Theme'),
            'data_attr'  => array(
                'widget-button' => array(
                    'event' => 'assign',
                    'related' => 'body',
                    'eventData' => array(
                        'theme_id' => $this->getThemeId()
                    )
                ),
            ),
            'class'   => 'save action-theme-assign',
            'target'  => '_blank'
        ));

        return $assignButton->toHtml();
    }
}
