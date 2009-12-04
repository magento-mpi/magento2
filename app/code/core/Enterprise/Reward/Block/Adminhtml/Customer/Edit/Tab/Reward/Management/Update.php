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
 * @package     Enterprise_Reward
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Reward update points form
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Update
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Getter
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::registry('current_customer');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Update
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('reward_');
        $form->setFieldNameSuffix('reward');
        $fieldset = $form->addFieldset('update_fieldset', array(
            'legend' => Mage::helper('enterprise_reward')->__('Update Reward Points Balance')
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('website', 'select', array(
                'name'  => 'website_id',
                'title' => Mage::helper('enterprise_reward')->__('Website'),
                'label' => Mage::helper('enterprise_reward')->__('Website'),
                'values' => Mage::getModel('adminhtml/system_store')->getWebsiteValuesForForm()
            ));
        }

        $fieldset->addField('store', 'select', array(
            'name'  => 'store_id',
            'title' => Mage::helper('enterprise_reward')->__('Store View'),
            'label' => Mage::helper('enterprise_reward')->__('Store View')
        ));

        $fieldset->addField('points_delta', 'text', array(
            'name'  => 'points_delta',
            'title' => Mage::helper('enterprise_reward')->__('Update Points'),
            'label' => Mage::helper('enterprise_reward')->__('Update Points'),
            'note'  => Mage::helper('enterprise_reward')->__('Enter Negative Number to Substract Balance')
        ));

        $fieldset->addField('comment', 'text', array(
            'name'  => 'comment',
            'title' => Mage::helper('enterprise_reward')->__('Comment'),
            'label' => Mage::helper('enterprise_reward')->__('Comment')
        ));

        $fieldset = $form->addFieldset('notification_fieldset', array(
            'legend' => Mage::helper('enterprise_reward')->__('Reward Points Notifications')
        ));
        $fieldset->addField('update_notification', 'checkbox', array(
            'name'    => 'reward_update_notification',
            'title'   => Mage::helper('enterprise_reward')->__('Reward Points Update'),
            'label'   => Mage::helper('enterprise_reward')->__('Reward Points Update'),
            'checked' => $this->getCustomer()->getRewardUpdateNotification()?true:false,
            'value'   => 1
        ));

        $fieldset->addField('warning_notification', 'checkbox', array(
            'name'    => 'reward_warning_notification',
            'title'   => Mage::helper('enterprise_reward')->__('Reward Points Warning'),
            'label'   => Mage::helper('enterprise_reward')->__('Reward Points Warning'),
            'checked' => $this->getCustomer()->getRewardWarningNotification()?true:false,
            'value' => 1
        ));

        $this->setForm($form);
        return parent::_prepareForm();
    }
}