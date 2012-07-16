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
    public function __construct()
    {
        switch (Mage::registry('store_type')) {
            case 'website':
                $this->_objectId = 'website_id';
                $saveLabel   = Mage::helper('Mage_Core_Helper_Data')->__('Save Website');
                $deleteLabel = Mage::helper('Mage_Core_Helper_Data')->__('Delete Website');
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

        parent::__construct();

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
                $editLabel = Mage::helper('Mage_Core_Helper_Data')->__('Edit Website');
                $addLabel  = Mage::helper('Mage_Core_Helper_Data')->__('New Website');
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

    protected function _prepareLayout()
    {
        if ($this->_blockGroup && $this->_controller && $this->_mode
            && !$this->_layout->getChildName($this->_nameInLayout, 'form')
        ) {
            $this->setChild('form', $this->getLayout()->createBlock(
                $this->_blockGroup
                    . '_Block_'
                    . str_replace(' ', '_', ucwords(str_replace('_', ' ', $this->_controller . '_' . $this->_mode)))
                    . '_Form' . '_' . ucwords(Mage::registry('store_type'))
            ));
        }
        return parent::_prepareLayout();
    }
}
