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
 *
 * @method bool|null getHasThemeAssigned() If there is a theme that assigned to the store view
 * @method Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons_Save setHasThemeAssigned(bool $flag)
 * @method Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons_Save setMode(bool $flag)
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons_Save
    extends Mage_Backend_Block_Widget_Button_Split
{
    /**
     * Current theme used for preview
     *
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * Init save button
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function init()
    {
        $theme = $this->getTheme();
        $themeType = $theme->getType();
        if ($themeType == Mage_Core_Model_Theme::TYPE_PHYSICAL) {
            $this->_initPhysical();
        } else if ($themeType == Mage_Core_Model_Theme::TYPE_VIRTUAL) {
            if ($theme->getDomainModel(Mage_Core_Model_Theme::TYPE_VIRTUAL)->isAssigned()) {
                $this->_initAssigned();
            } else {
                $this->_initUnAssigned();
            }
        } else {
            throw new InvalidArgumentException(
                sprintf('Invalid theme of a "%s" type passed to save button block', $themeType)
            );
        }

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
            throw new InvalidArgumentException('Current theme was not passed to save button');
        }
        return $this->_theme;
    }

    /**
     * Set current theme
     *
     * @param Mage_Core_Model_Theme $theme
     * @return $this
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
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/system_design_editor/save', array('theme_id' => $this->getTheme()->getId()));
    }

    /**
     * Init 'Save' button for 'physical' theme
     *
     * @return $this
     */
    protected function _initPhysical()
    {
        $this->setData(array(
            'label'          => $this->__('Assign'),
            'data_attribute' => array('mage-init' => $this->_getAssignInitData()),
            'options'        => array()
        ));

        return $this;
    }

    /**
     * Init 'Save' button for 'virtual' theme assigned to a store
     *
     * @return $this
     */
    protected function _initAssigned()
    {
        $this->setData(array(
            'label'          => $this->__('Save'),
            'data_attribute' => array('mage-init' => $this->_getSaveAssignedInitData()),
            'options'        => array()
        ));

        return $this;
    }

    /**
     * Init 'Save' button for 'virtual' theme assigned to a store
     *
     * @return $this
     */
    protected function _initUnAssigned()
    {
        $this->setData(array(
            'label'          => $this->__('Save'),
            'data_attribute' => array('mage-init' => $this->_getSaveInitData()),
            'options'        => array(
                array(
                    'label'          => $this->__('Save & Assign'),
                    'data_attribute' => array('mage-init' => $this->_getSaveAndAssignInitData())
                ),
            )
        ));

        return $this;
    }

    /**
     * Get 'data-mage-init' attribute value for 'Save' button
     *
     * @return string
     */
    protected function _getSaveInitData()
    {
        $data = array(
            'button' => array(
                'event'     => 'save',
                'target'    => 'body',
                'eventData' => array(
                    'theme_id' => $this->getTheme()->getId(),
                    'save_url' => $this->getSaveUrl(),
                    'confirm'  => false
                )
            ),
        );

        return $this->_encode($data);
    }

    /**
     * Get 'data-mage-init' attribute value for 'Save' button when theme is live (assigned)
     *
     * @return string
     */
    protected function _getSaveAssignedInitData()
    {
        $message = $this->__("You changed the design of your live store. Are you sure you want to do that?");
        $title = $this->__("Save");

        $data = array(
            'button' => array(
                'event'     => 'save',
                'target'    => 'body',
                'eventData' => array(
                    'theme_id' => $this->getTheme()->getId(),
                    'save_url' => $this->getSaveUrl(),
                    'confirm' => array(
                        'message' => $message,
                        'title'   => $title,
                        'buttons' => array()
                    )
                )
            ),
        );

        return $this->_encode($data);
    }

    /**
     * Get 'data-mage-init' attribute value for 'Save' button
     *
     * @return string
     */
    protected function _getAssignInitData()
    {
        $message = $this->__("Are you sure you want to change the theme of your live store?");
        $title = $this->__("Assign");

        $data = array(
            'button' => array(
                'event'     => 'assign',
                'target'    => 'body',
                'eventData' => array(
                    'theme_id'        => $this->getTheme()->getId(),
                    'confirm' => array(
                        'message' => $this->__($message),
                        'title'   => $title
                    )
                )
            ),
        );

        return $this->_encode($data);
    }

    /**
     * Get 'data-mage-init' attribute value for 'Save and Assign' button
     *
     * Used in VDE when clicking button on top toolbar
     *
     * @return string
     */
    protected function _getSaveAndAssignInitData()
    {
        if ($this->getHasThemeAssigned()) {
            $message = $this->__("Are you sure you want this theme to replace your current theme?");
        } else {
            $message = $this->__("Do you want to use this theme in your live store?");
        }
        $title = $this->__("Save & Assign");

        $data = array(
            'button' => array(
                'event'     => 'save-and-assign',
                'target'    => 'body',
                'eventData' => array(
                    'theme_id' => $this->getTheme()->getId(),
                    'save_url' => $this->getSaveUrl(),
                    'confirm'  => array(
                        'message' => $message,
                        'title'   => $title
                    )
                )
            ),
        );

        return $this->_encode($data);
    }

    /**
     * Get encoded data string
     *
     * @param array $data
     * @return string
     */
    protected function _encode($data)
    {
        return $this->helper('Mage_Backend_Helper_Data')->escapeHtml(json_encode($data));
    }
}
