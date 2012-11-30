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
 * Theme selector tab for available themes
 */
class Mage_DesignEditor_Block_Adminhtml_Theme_Selector_Tab_Available
    extends Mage_Backend_Block_Template
    implements Mage_Backend_Block_Widget_Tab_Interface
{
    /**
     * TODO Remove this method after implementing
     * Temporary tab content
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getTabTitle();
    }

    /**
     * Initialize tab block
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addData(array(
            'class' => 'ajax',
         ));
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Available Themes');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->getTabTitle();
    }

    /**
     * Return url for ajax loading tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('*/*/availableThemes');
    }

    /**
     * Return css tab class
     *
     * @return string
     */
    public function getTabClass()
    {
        return $this->getClass();
    }


    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
