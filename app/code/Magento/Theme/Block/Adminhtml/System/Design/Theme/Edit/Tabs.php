<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme editor tab container
 */
class Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tabs extends Magento_Backend_Block_Widget_Tabs
{
    /**
     * Initialize tabs and define tabs block settings
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('theme_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Theme'));
    }
}
