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
 * Design editor theme
 *
 * @method Mage_DesignEditor_Block_Adminhtml_Theme setTheme(Mage_Core_Model_Theme $theme)
 * @method Mage_Core_Model_Theme getTheme()
 */
class Mage_DesignEditor_Block_Adminhtml_Theme extends Mage_Backend_Block_Template
{
    /**
     * Buttons array
     *
     * @var array()
     */
    protected $_buttons = array();

    /**
     * Add button
     *
     * @param Mage_Backend_Block_Widget_Button $button
     * @return Mage_DesignEditor_Block_Adminhtml_Theme
     */
    public function addButton($button)
    {
        $this->_buttons[] = $button;
        return $this;
    }

    /**
     * Clear buttons
     *
     * @return Mage_DesignEditor_Block_Adminhtml_Theme
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
        /** @var $button Mage_Backend_Block_Widget_Button */
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
        foreach ($this->getTheme()->getAssignedStores() as $store) {
            $storesTitles[] = $store->getFrontendName();
        }
        return $storesTitles;
    }
}
