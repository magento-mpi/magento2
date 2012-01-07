<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cms Page Tree Edit Form Container Block
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize Form Container
     *
     */
    public function __construct()
    {
        $this->_objectId   = 'node_id';
        $this->_blockGroup = 'Enterprise_Cms';
        $this->_controller = 'adminhtml_cms_hierarchy';

        parent::__construct();

        $this->_updateButton('save', 'onclick', 'hierarchyNodes.save()');
        $this->_updateButton('save', 'label', Mage::helper('Enterprise_Cms_Helper_Data')->__('Save Pages Hierarchy'));
        $this->_removeButton('back');
    }

    /**
     * Retrieve text for header element
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('Enterprise_Cms_Helper_Data')->__('Manage Pages Hierarchy');
    }
}