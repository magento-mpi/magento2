<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme editor tab container
 */
class Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tabs extends Magento_Backend_Block_Widget_Tabs
{
    /**
     * Initialize tabs and define tabs block settings
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('theme_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('Theme'));
    }
}
