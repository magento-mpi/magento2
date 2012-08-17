<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme editor tab container
 */
class Mage_Adminhtml_Block_System_Design_Theme_Edit_Tabs extends Mage_Backend_Block_Widget_Tabs
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('theme_tabs');
        $this->setDestElementId('theme_edit_form');
        $this->setTitle(Mage::helper('Mage_Core_Helper_Data')->__('Theme'));
    }
}
