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
 * Adminhtml store edit
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_System_Store_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     *
     */
    protected function _construct()
    {
        switch (Mage::registry('store_type')) {
            case 'website':
                $this->_objectId = 'website_id';
                $saveLabel   = Mage::helper('Mage_Core_Helper_Data')->__('Save Web Site');
                $deleteLabel = Mage::helper('Mage_Core_Helper_Data')->__('Delete Web Site');
                $deleteUrl   = $this->getUrl(
                    '*/*/deleteWebsite',
                    array('item_id' => Mage::registry('store_data')->getId())
                );
                break;
            case 'group':
                $this->_objectId = 'group_id';
                $saveLabel   = Mage::helper('Mage_Core_Helper_Data')->__('Save Store');
                $deleteLabel = Mage::helper('Mage_Core_Helper_Data')->__('Delete Store');
                $deleteUrl   = $this->getUrl(
                    '*/*/deleteGroup',
                    array('item_id' => Mage::registry('store_data')->getId())
                );
                break;
            case 'store':
                $this->_objectId = 'store_id';
                $saveLabel   = Mage::helper('Mage_Core_Helper_Data')->__('Save Store View');
                $deleteLabel = Mage::helper('Mage_Core_Helper_Data')->__('Delete Store View');
                $deleteUrl   = $this->getUrl(
                    '*/*/deleteStore',
                    array('item_id' => Mage::registry('store_data')->getId())
                );
                break;
            default:
                $saveLabel = '';
                $deleteLabel = '';
                $deleteUrl = '';
        }
        $this->_controller = 'system_store';

        parent::_construct();

        $this->_updateButton('save', 'label', $saveLabel);
        $this->_updateButton('delete', 'label', $deleteLabel);
        $this->_updateButton('delete', 'onclick', 'setLocation(\''.$deleteUrl.'\');');

        if (!Mage::registry('store_data')) {
            return;
        }

        if (!Mage::registry('store_data')->isCanDelete()) {
            $this->_removeButton('delete');
        }
        if (Mage::registry('store_data')->isReadOnly()) {
            $this->_removeButton('save')->_removeButton('reset');
        }
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        switch (Mage::registry('store_type')) {
            case 'website':
                $editLabel = Mage::helper('Mage_Core_Helper_Data')->__('Edit Web Site');
                $addLabel  = Mage::helper('Mage_Core_Helper_Data')->__('New Web Site');
                break;
            case 'group':
                $editLabel = Mage::helper('Mage_Core_Helper_Data')->__('Edit Store');
                $addLabel  = Mage::helper('Mage_Core_Helper_Data')->__('New Store');
                break;
            case 'store':
                $editLabel = Mage::helper('Mage_Core_Helper_Data')->__('Edit Store View');
                $addLabel  = Mage::helper('Mage_Core_Helper_Data')->__('New Store View');
                break;
        }

        return Mage::registry('store_action') == 'add' ? $addLabel : $editLabel;
    }

    /**
     * Build child form class form name based on value of store_type in registry
     *
     * @return string
     */
    protected function _buildFormClassName()
    {
        return parent::_buildFormClassName() . '_' . ucwords(Mage::registry('store_type'));
    }
}
