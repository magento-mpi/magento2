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
 * Reward rate edit form
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Adminhtml_Reward_Rate_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Getter
     *
     * @return Enterprise_Reward_Model_Reward_Rate
     */
    public function getRate()
    {
        return Mage::registry('current_reward_rate');
    }

    /**
     * Prepare form
     *
     * @return Enterprise_Reward_Block_Adminhtml_Reward_Rate_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('_current' => true)),
            'method' => 'post'
        ));
        $form->setFieldNameSuffix('rate');
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('enterprise_reward')->__('Base Fieldset')
        ));

        $websites = Mage::getSingleton('adminhtml/system_store')
            ->getWebsiteOptionHash();
        $websites = array_merge(array(Mage::helper('enterprise_reward')->__('All Websites')), $websites);
        $fieldset->addField('website_id', 'select', array(
            'name'   => 'website_id',
            'title'  => Mage::helper('enterprise_reward')->__('Website'),
            'label'  => Mage::helper('enterprise_reward')->__('Website'),
            'values' => $websites
        ));

        $fieldset->addField('customer_group_id', 'select', array(
            'name'   => 'customer_group_id',
            'title'  => Mage::helper('enterprise_reward')->__('Customer Group'),
            'label'  => Mage::helper('enterprise_reward')->__('Customer Group'),
            'values' => $this->getCustomerGroups()
        ));

        $rateRenderer = $this->getLayout()
            ->createBlock('enterprise_reward/adminhtml_reward_rate_edit_form_renderer_rate')
            ->setRate($this->getRate());

        $fieldset->addField('points_to_currency', 'note', array(
            'title'         => Mage::helper('enterprise_reward')->__('Points to Currency Rate'),
            'label'         => Mage::helper('enterprise_reward')->__('Points to Currency Rate'),
            'balance_index' => 'points_count',
            'value_index'   => 'points_currency_value'
        ))->setRenderer($rateRenderer);

        $fieldset->addField('currency_to_points', 'note', array(
            'title' => Mage::helper('enterprise_reward')->__('Currency to Points Rate'),
            'label' => Mage::helper('enterprise_reward')->__('Currency to Points Rate'),
            'balance_index' => 'currency_amount',
            'value_index'   => 'currency_points_value'
        ))->setRenderer($rateRenderer);

        $form->setUseContainer(true);
        $form->setValues($this->getRate()->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getCustomerGroups()
    {
        $groups = Mage::getResourceModel('customer/group_collection')
            ->load()
            ->toOptionHash();
        $groups = array_merge(array(
            'all' => Mage::helper('enterprise_reward')->__('All Customer Groups'
        )),  $groups);
        return $groups;
    }
}