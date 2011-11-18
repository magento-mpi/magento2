<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize banner edit page tabs
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('banner_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Enterprise_Banner_Helper_Data')->__('Banner Information'));
    }
}
