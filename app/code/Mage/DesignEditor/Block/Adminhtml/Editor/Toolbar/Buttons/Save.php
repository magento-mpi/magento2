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
 * Save button block
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons_Save
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_BlockAbstract
{
    /**
     * Current theme used for preview
     *
     * @var int
     */
    protected $_themeId;

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
     * Get 'data-mage-init' attribute value for 'Save' button
     *
     * @return string
     */
    public function getSaveInitData()
    {
        $data = array(
            'button' => array(
                'event'     => 'save',
                'target'    => 'body',
                'eventData' => array(
                    'theme_id' => $this->getThemeId(),
                    'save_url' => $this->getSaveUrl(),
                )
            ),
        );

        return $this->helper('Mage_Backend_Helper_Data')->escapeHtml(json_encode($data));
    }

    /**
     * Get URL to apply changes from 'staging' theme to 'virtual' theme
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/system_design_editor/save');
    }

    /**
     * Get 'data-mage-init' attribute value for 'Save and Assign' button
     *
     * @return string
     */
    public function getSaveAndAssignInitData()
    {
        $data = array(
            'button' => array(
                'event'     => 'save-and-assign',
                'target'    => 'body',
                'eventData' => array(
                    'theme_id' => $this->getThemeId(),
                    'save_url' => $this->getSaveUrl(),
                )
            ),
        );

        return $this->helper('Mage_Backend_Helper_Data')->escapeHtml(json_encode($data));
    }
}
