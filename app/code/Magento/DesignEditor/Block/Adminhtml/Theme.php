<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design editor theme
 *
 * @method Magento_DesignEditor_Block_Adminhtml_Theme setTheme(Magento_Core_Model_Theme $theme)
 * @method Magento_Core_Model_Theme getTheme()
 */
class Magento_DesignEditor_Block_Adminhtml_Theme extends Magento_Backend_Block_Template
{
    /**
     * Buttons array
     *
     * @var array
     */
    protected $_buttons = array();

    /**
     * Add button
     *
     * @param Magento_Backend_Block_Widget_Button $button
     * @return Magento_DesignEditor_Block_Adminhtml_Theme
     */
    public function addButton($button)
    {
        $this->_buttons[] = $button;
        return $this;
    }

    /**
     * Clear buttons
     *
     * @return Magento_DesignEditor_Block_Adminhtml_Theme
     */
    public function clearButtons()
    {
        $this->_buttons = array();
        return $this;
    }

    /**
     * Get buttons html
     *
     * @return string
     */
    public function getButtonsHtml()
    {
        $output = '';
        /** @var $button Magento_Backend_Block_Widget_Button */
        foreach ($this->_buttons as $button) {
            $output .= $button->toHtml();
        }
        return $output;
    }

    /**
     * Return array of assigned stores titles
     *
     * @return array
     */
    public function getStoresTitles()
    {
        $storesTitles = array();
        /** @var $store Magento_Core_Model_Store */
        foreach ($this->getTheme()->getAssignedStores() as $store) {
            $storesTitles[] = $store->getName();
        }
        return $storesTitles;
    }

    /**
     * Get options for JS widget vde.themeControl
     *
     * @return string
     */
    public function getOptionsJson()
    {
        $theme = $this->getTheme();
        $options = array(
            'theme_id'    => $theme->getId(),
            'theme_title' => $theme->getThemeTitle()
        );

        /** @var $helper Magento_Core_Helper_Data */
        $helper = $this->helper('Magento_Core_Helper_Data');
        return $helper->jsonEncode($options);
    }

    /**
     * Get quick save button
     *
     * @return Magento_Backend_Block_Widget_Button
     */
    public function getQuickSaveButton()
    {
        /** @var $saveButton Magento_Backend_Block_Widget_Button */
        $saveButton = $this->getLayout()->createBlock('Magento_Backend_Block_Widget_Button');
        $saveButton->setData(array(
            'label'     => __('Save'),
            'class'     => 'action-save',
        ));
        return $saveButton;
    }
}
