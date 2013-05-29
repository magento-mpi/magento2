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
 * Edit button block
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons_Edit
    extends Mage_Backend_Block_Widget_Button_Split
{
    /**
     * Current theme
     *
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * Init edit button
     *
     * @return $this
     */
    public function init()
    {
        $this->_initEditButton();
        return $this;
    }

    /**
     * Get current theme
     *
     * @return Mage_Core_Model_Theme
     * @throws InvalidArgumentException
     */
    public function getTheme()
    {
        if (null === $this->_theme) {
            throw new InvalidArgumentException('Current theme was not passed to edit button');
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
     * Whether button is disabled
     *
     * @return mixed
     */
    public function getDisabled()
    {
        return false;
    }

    /**
     * Disable actions-split functionality if no options provided
     *
     * @return bool
     */
    public function hasSplit()
    {
        $options = $this->getOptions();
        return is_array($options) && count($options) > 0;
    }

    /**
     * Get URL to apply changes from 'staging' theme to 'virtual' theme
     *
     * @param string $revertType
     * @return string
     */
    public function getRevertUrl($revertType)
    {
        return $this->getUrl('*/system_design_editor/revert', array(
            'theme_id'  => $this->getTheme()->getId(),
            'revert_to' => $revertType
        ));
    }

    /**
     * Init 'Edit' button for 'physical' theme
     *
     * @return $this
     */
    protected function _initEditButton()
    {
        $this->setData(array(
            'label'          => $this->__('Edit'),
            'options'        => array(
                array(
                    'label'          => $this->__('Revert Styles to Last Saved'),
                    'data_attribute' => array('mage-init' => $this->_getAssignInitData('revert-to-last', 'last_saved'))
                ),
                array(
                    'label'          => $this->__('Revert Styles to Theme Default Values'),
                    'data_attribute' => array('mage-init' => $this->_getAssignInitData('revert-to-default', 'physical'))
                )
            )
        ));

        return $this;
    }

    /**
     * Get 'data-mage-init' attribute value for 'Edit' button
     *
     * @param string $event
     * @param string $revertType
     * @return string
     */
    protected function _getAssignInitData($event, $revertType)
    {
        $data = array(
            'vde-edit-button' => array(
                'event'     => $event,
                'target'    => 'body',
                'eventData' => array(
                    'theme_id' => $this->getTheme()->getId(),
                    'url'      => $this->getRevertUrl($revertType)
                )
            ),
        );
        return $this->helper('Mage_Backend_Helper_Data')->escapeHtml(json_encode($data));
    }
}
