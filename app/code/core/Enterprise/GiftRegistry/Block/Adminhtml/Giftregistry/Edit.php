<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit extends Enterprise_Enterprise_Block_Adminhtml_Widget_Form_Container
{
    /**
     * Intialize form
     *
     * @return void
     */
    public function __construct()
    {
        $this->_blockGroup = 'enterprise_giftregistry';
        $this->_controller = 'adminhtml_giftregistry';

        parent::__construct();

        if (Mage::registry('current_giftregistry_type')) {
            $this->_updateButton('save', 'label', Mage::helper('enterprise_giftregistry')->__('Save'));
            $this->_updateButton('save', 'onclick', 'editForm.submit(\'' . $this->getSaveUrl() . '\');');
            $this->_updateButton('delete', 'label', Mage::helper('enterprise_giftregistry')->__('Delete'));

            $this->_addButton('save_and_continue_edit', array(
                'class'   => 'save',
                'label'   => Mage::helper('enterprise_giftregistry')->__('Save and Continue Edit'),
                'onclick' => 'editForm.submit(\'' . $this->getSaveUrl() . '\' + \'back/edit/\')',
            ), 3);
        }
    }

    /**
     * Return form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $type = Mage::registry('current_giftregistry_type');
        if ($type->getTypeId()) {
            return Mage::helper('enterprise_giftregistry')->__("Edit '%s' Gift Registry Type", $this->htmlEscape($type->getLabel()));
        }
        else {
            return Mage::helper('enterprise_giftregistry')->__('New Gift Registry Type');
        }
    }

    /**
     * Return save url
     *
     * @return string
     */
    public function getSaveUrl()
    {
        $storeId = Mage::registry('current_giftregistry_type')->getStoreId();

        return $this->getUrl('*/*/save', array(
            'store'   => $storeId
        ));
    }
}
