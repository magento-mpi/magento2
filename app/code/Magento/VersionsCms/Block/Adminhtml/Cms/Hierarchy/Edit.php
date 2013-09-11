<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cms Page Tree Edit Form Container Block
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    /**
     * Initialize Form Container
     *
     */
    protected function _construct()
    {
        $this->_objectId   = 'node_id';
        $this->_blockGroup = 'Magento_VersionsCms';
        $this->_controller = 'adminhtml_cms_hierarchy';

        parent::_construct();

        $this->_updateButton('save', 'onclick', 'hierarchyNodes.save()');
        $this->_removeButton('back');
        $this->_addButton('delete', array(
            'label'     => __('Delete Current Hierarchy'),
            'class'     => 'delete',
            'onclick'   => 'deleteCurrentHierarchy()',
        ), -1, 1);

        if (!\Mage::app()->hasSingleStore()) {
            $this->_addButton('delete_multiple', array(
                'label'     => \Mage::helper('Magento\VersionsCms\Helper\Data')->getDeleteMultipleHierarchiesText(),
                'class'     => 'delete',
                'onclick'   => "openHierarchyDialog('delete')",
            ), -1, 7);
            $this->_addButton('copy', array(
                'label'     => __('Copy'),
                'class'     => 'add',
                'onclick'   => "openHierarchyDialog('copy')",
            ), -1, 14);
        }
    }

    /**
     * Retrieve text for header element
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Pages Hierarchy');
    }
}
