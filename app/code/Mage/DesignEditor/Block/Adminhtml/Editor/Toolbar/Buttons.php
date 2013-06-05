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
 *
 * @method Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons setVirtualThemeId(int $id)
 * @method int getVirtualThemeId()
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons
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
     * Get assign to store view button
     *
     * @return string
     */
    public function getAssignButtonHtml()
    {
        $message = "Are you sure you want to change the theme of your live store?";

        /** @var $assignButton Mage_Backend_Block_Widget_Button */
        $assignButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');
        $assignButton->setData(array(
            'label'  => $this->__('Assign Theme'),
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array(
                        'event'     => 'assign',
                        'target'    => 'body',
                        'eventData' => array(
                            'theme_id'        => $this->getThemeId(),
                            'confirm_message' =>  $this->__($message)
                        )
                    ),
                ),
            ),
            'class'  => 'save action-theme-assign',
            'target' => '_blank'
        ));

        return $assignButton->toHtml();
    }

    /**
     * Get admin panel home page URL
     *
     * @return string
     */
    public function getHomeLink()
    {
        return $this->helper('Mage_Backend_Helper_Data')->getHomePageUrl();
    }
}
