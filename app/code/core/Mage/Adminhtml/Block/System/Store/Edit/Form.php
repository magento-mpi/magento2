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
 * Adminhtml store edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Victor Tihonchuk <victor@varien.com>
 */

class Mage_Adminhtml_Block_System_Store_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('coreStoreForm');
    }

    /**
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        if (Mage::registry('store_type') == 'website') {
            $websiteModel = Mage::registry('store_data');
            $showWebsiteFieldset = true;
            $showGroupFieldset = $showStoreFieldset = false;
//            if (Mage::registry('store_action') == 'add') {
//                $groupModel = Mage::getModel('core/store_group');
//                $storeModel = Mage::getModel('core/store');
//                $showGroupFieldset = $showStoreFieldset = true;
//            }
        }
        elseif (Mage::registry('store_type') == 'group') {
            $groupModel = Mage::registry('store_data');
            $showGroupFieldset = true;
            $showWebsiteFieldset = $showStoreFieldset = false;
//            if (Mage::registry('store_action') == 'add') {
//                $storeModel = Mage::getModel('core/store');
//                $showStoreFieldset = true;
//            }
        }
        elseif (Mage::registry('store_type') == 'store') {
            $storeModel = Mage::registry('store_data');
            $showWebsiteFieldset = $showGroupFieldset = false;
            $showStoreFieldset = true;
        }

        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'POST'
        ));

        if ($showWebsiteFieldset) {
            if ($postData = Mage::registry('store_post_data')) {
                $websiteModel->setData($postData['website']);
            }
            $fieldset = $form->addFieldset('website_fieldset', array(
                'legend' => Mage::helper('core')->__('Website Information')
            ));
            /* @var $fieldset Varien_Data_Form */

            $fieldset->addField('website_name', 'text', array(
                'name'      => 'website[name]',
                'label'     => Mage::helper('core')->__('Name'),
                'value'     => $websiteModel->getName(),
                'required'  => true
            ));

            $fieldset->addField('website_code', 'text', array(
                'name'      => 'website[code]',
                'label'     => Mage::helper('core')->__('Code'),
                'value'     => $websiteModel->getCode(),
                'required'  => true
            ));

            $fieldset->addField('website_sort_order', 'text', array(
                'name'      => 'website[sort_order]',
                'label'     => Mage::helper('core')->__('Sort order'),
                'value'     => $websiteModel->getSortOrder(),
                'required'  => false
            ));

            if (Mage::registry('store_action') == 'edit') {
                $groups = Mage::getModel('core/store_group')->getCollection()->addWebsiteFilter($websiteModel->getId())->toOptionArray();
                //array_unshift($groups, array('label'=>'', 'value'=>0));
                $fieldset->addField('website_default_group_id', 'select', array(
                    'name'      => 'website[default_group_id]',
                    'label'     => Mage::helper('core')->__('Default Store'),
                    'value'     => $websiteModel->getDefaultGroupId(),
                    'values'    => $groups,
                    'required'  => false
                ));
            }

            $fieldset->addField('website_website_id', 'hidden', array(
                'name'  => 'website[website_id]',
                'value' => $websiteModel->getId()
            ));
        }

        if ($showGroupFieldset) {
            if ($postData = Mage::registry('store_post_data')) {
                $groupModel->setData($postData['group']);
            }
            $fieldset = $form->addFieldset('group_fieldset', array(
                'legend' => Mage::helper('core')->__('Store Information')
            ));

            if (Mage::registry('store_action') == 'edit'
                || (Mage::registry('store_action') == 'add' && Mage::registry('store_type') == 'group')) {
                $websites = Mage::getModel('core/website')->getCollection()->toOptionArray();
                $fieldset->addField('group_website_id', 'select', array(
                    'name'      => 'group[website_id]',
                    'label'     => Mage::helper('core')->__('Website'),
                    'value'     => $groupModel->getWebsiteId(),
                    'values'    => $websites,
                    'required'  => true
                ));
                if ($groupModel->getId() && $groupModel->getWebsite()->getDefaultGroupId() == $groupModel->getId()) {
                    if ($groupModel->getWebsite() && $groupModel->getWebsite()->getGroupsCount() > 1) {
                        $form->getElement('group_website_id')->setDisabled(true);

                        $fieldset->addField('group_hidden_website_id', 'hidden', array(
                            'name'      => 'group[website_id]',
                            'no_span'   => true,
                            'value'     => $groupModel->getWebsiteId()
                        ));
                    }
                    else {
                        $fieldset->addField('group_original_website_id', 'hidden', array(
                            'name'      => 'group[original_website_id]',
                            'no_span'   => true,
                            'value'     => $groupModel->getWebsiteId()
                        ));
                    }
                }
            }

            $fieldset->addField('group_name', 'text', array(
                'name'      => 'group[name]',
                'label'     => Mage::helper('core')->__('Name'),
                'value'     => $groupModel->getName(),
                'required'  => true
            ));

            $categories = Mage::getModel('adminhtml/system_config_source_category')->toOptionArray();

            $fieldset->addField('group_root_category_id', 'select', array(
                'name'      => 'group[root_category_id]',
                'label'     => Mage::helper('core')->__('Root Category'),
                'value'     => $groupModel->getRootCategoryId(),
                'values'    => $categories,
                'required'  => true
            ));

            if (Mage::registry('store_action') == 'edit') {
                $stores = Mage::getModel('core/store')->getCollection()->addGroupFilter($groupModel->getId())->toOptionArray();
                //array_unshift($stores, array('label'=>'', 'value'=>0));
                $fieldset->addField('group_default_store_id', 'select', array(
                    'name'      => 'group[default_store_id]',
                    'label'     => Mage::helper('core')->__('Default Store View'),
                    'value'     => $groupModel->getDefaultStoreId(),
                    'values'    => $stores,
                    'required'  => false
                ));
            }

            $fieldset->addField('group_group_id', 'hidden', array(
                'name'      => 'group[group_id]',
                'no_span'   => true,
                'value'     => $groupModel->getId()
            ));
        }

        if ($showStoreFieldset) {
            if ($postData = Mage::registry('store_post_data')) {
                $storeModel->setData($postData['store']);
            }
            $fieldset = $form->addFieldset('store_fieldset', array(
                'legend' => Mage::helper('core')->__('Store View Information')
            ));

            if (Mage::registry('store_action') == 'edit'
                || Mage::registry('store_action') == 'add' && Mage::registry('store_type') == 'store') {
                $websites = Mage::getModel('core/website')->getCollection();
                $allgroups = Mage::getModel('core/store_group')->getCollection();
                $groups = array();
                foreach ($websites as $website) {
                    $values = array();
                    foreach ($allgroups as $group) {
                        if ($group->getWebsiteId() == $website->getId()) {
                            $values[] = array('label'=>$group->getName(),'value'=>$group->getId());
                        }
                    }
                    $groups[] = array('label'=>$website->getName(),'value'=>$values);
                }
                $fieldset->addField('store_group_id', 'select', array(
                    'name'      => 'store[group_id]',
                    'label'     => Mage::helper('core')->__('Store'),
                    'value'     => $storeModel->getGroupId(),
                    'values'    => $groups,
                    'required'  => true
                ));
                if ($storeModel->getId() && $storeModel->getGroup()->getDefaultStoreId() == $storeModel->getId()) {
                    if ($storeModel->getGroup() && $storeModel->getGroup()->getStoresCount() > 1) {
                        $form->getElement('store_group_id')->setDisabled(true);

                        $fieldset->addField('store_hidden_group_id', 'hidden', array(
                            'name'      => 'store[group_id]',
                            'no_span'   => true,
                            'value'     => $storeModel->getGroupId()
                        ));
                    }
                    else {
                        $fieldset->addField('store_original_group_id', 'hidden', array(
                            'name'      => 'store[original_group_id]',
                            'no_span'   => true,
                            'value'     => $storeModel->getGroupId()
                        ));
                    }
                }
            }

            $fieldset->addField('store_name', 'text', array(
                'name'      => 'store[name]',
                'label'     => Mage::helper('core')->__('Name'),
                'value'     => $storeModel->getName(),
                'required'  => true
            ));
            $fieldset->addField('store_code', 'text', array(
                'name'      => 'store[code]',
                'label'     => Mage::helper('core')->__('Code'),
                'value'     => $storeModel->getCode(),
                'required'  => true
            ));

            $fieldset->addField('store_is_active', 'select', array(
                'name'      => 'store[is_active]',
                'label'     => Mage::helper('core')->__('Status'),
                'value'     => $storeModel->getIsActive(),
                'options'   => array(
                    0 => Mage::helper('adminhtml')->__('Disabled'),
                    1 => Mage::helper('adminhtml')->__('Enabled')),
                'required'  => true
            ));

            $fieldset->addField('store_sort_order', 'text', array(
                'name'      => 'store[sort_order]',
                'label'     => Mage::helper('core')->__('Sort order'),
                'value'     => $storeModel->getSortOrder(),
                'required'  => false
            ));

            $fieldset->addField('store_is_default', 'hidden', array(
                'name'      => 'store[is_default]',
                'no_span'   => true,
                'value'     => $storeModel->getIsDefault(),
            ));

            $fieldset->addField('store_store_id', 'hidden', array(
                'name'      => 'store[store_id]',
                'no_span'   => true,
                'value'     => $storeModel->getId()
            ));
        }

        $form->addField('store_type', 'hidden', array(
            'name'      => 'store_type',
            'no_span'   => true,
            'value'     => Mage::registry('store_type')
        ));

        $form->addField('store_action', 'hidden', array(
            'name'      => 'store_action',
            'no_span'   => true,
            'value'     => Mage::registry('store_action')
        ));

        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}