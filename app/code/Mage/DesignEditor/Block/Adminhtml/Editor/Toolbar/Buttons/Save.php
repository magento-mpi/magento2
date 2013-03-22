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
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * Get current theme
     *
     * @return Mage_Core_Model_Theme
     * @throws InvalidArgumentException
     */
    public function getTheme()
    {
        if (null === $this->_theme) {
            throw new InvalidArgumentException('Current theme was not passed to save button');
        }
        return $this->_theme;
    }

    /**
     * Set current theme
     *
     * @param Mage_Core_Model_Theme $theme
     * @return Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons
     */
    public function setTheme($theme)
    {
        $this->_theme = $theme;

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
                    'theme_id' => $this->getTheme()->getId(),
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
        return $this->getUrl('*/system_design_editor/save', array('theme_id' => $this->getTheme()->getId()));
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
                    'theme_id' => $this->getTheme()->getId(),
                    'save_url' => $this->getSaveUrl(),
                )
            ),
        );

        return $this->helper('Mage_Backend_Helper_Data')->escapeHtml(json_encode($data));
    }

    /**
     * Whether button save is enable
     *
     * @return bool
     */
    public function isEnable()
    {
        return $this->getTheme()->isEditable();
    }
}
