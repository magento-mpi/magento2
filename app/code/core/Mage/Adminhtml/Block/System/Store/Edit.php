<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml store edit
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Victor Tihonchuk <victor@varien.com>
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
                $saveLabel   = Mage::helper('core')->__('Save Website');
                $deleteLabel = Mage::helper('core')->__('Delete Website');
                $deleteUrl   = $this->getUrl('*/*/deleteWebsite', array('website_id'=>Mage::registry('store_data')->getId()));
                break;
            case 'group':
                $this->_objectId = 'group_id';
                $saveLabel   = Mage::helper('core')->__('Save Store');
                $deleteLabel = Mage::helper('core')->__('Delete Store');
                $deleteUrl   = $this->getUrl('*/*/deleteGroup', array('group_id'=>Mage::registry('store_data')->getId()));
                break;
            case 'store':
                $this->_objectId = 'store_id';
                $saveLabel   = Mage::helper('core')->__('Save Language');
                $deleteLabel = Mage::helper('core')->__('Delete Language');
                $deleteUrl   = $this->getUrl('*/*/deleteStore', array('store_id'=>Mage::registry('store_data')->getId()));
                break;
        }
        $this->_controller = 'system_store';

        parent::__construct();

        $this->_updateButton('save', 'label', $saveLabel);
        $this->_updateButton('delete', 'label', $deleteLabel);
        $this->_updateButton('delete', 'onclick', 'setLocation(\''.$deleteUrl.'\');');

        if (!Mage::registry('store_data')->isCanDelete()) {
            $this->_removeButton('delete');
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
                $editLabel = Mage::helper('core')->__('Edit Website');
                $addLabel  = Mage::helper('core')->__('New Website');
                break;
            case 'group':
                $editLabel = Mage::helper('core')->__('Edit Store');
                $addLabel  = Mage::helper('core')->__('New Store');
                break;
            case 'store':
                $editLabel = Mage::helper('core')->__('Edit Language');
                $addLabel  = Mage::helper('core')->__('New Language');
                break;
        }

        return Mage::registry('store_action') == 'add' ? $addLabel : $editLabel;
    }
}