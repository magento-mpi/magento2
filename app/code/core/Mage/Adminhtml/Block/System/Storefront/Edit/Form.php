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
 * Adminhtml storefront edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Victor Tihonchuk <victor@varien.com>
 */

class Mage_Adminhtml_Block_System_Storefront_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('coreStorefrontForm');
    }

    /**
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        if (Mage::registry('storefront_type') == 'website') {
            $websiteModel = Mage::registry('storefront_data');
            $showWebsiteFieldset = true;
            $showGroupFieldset = $showStoreFieldset = false;
            if (Mage::registry('storefront_action') == 'add') {
                $groupModel = Mage::getModel('core/store_group')->load(null);
                $storeModel = Mage::getModel('core/store')->load(null);
                $showGroupFieldset = $showStoreFieldset = true;
            }
        }
        elseif (Mage::registry('storefront_type') == 'group') {
            $groupModel = Mage::registry('storefront_data');
            $showGroupFieldset = true;
            $showWebsiteFieldset = $showStoreFieldset = false;
            if (Mage::registry('storefront_action') == 'add') {
                $storeModel = Mage::getModel('core/store')->load(null);
                $showStoreFieldset = true;
            }
        }
        elseif (Mage::registry('storefront_type') == 'store') {
            $storeModel = Mage::registry('storefront_data');
            $showWebsiteFieldset = $showGroupFieldset = false;
            $showStoreFieldset = true;
        }

        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'POST'
        ));

        if ($showWebsiteFieldset) {
            if ($postData = Mage::registry('storefront_post_data')) {
                $websiteModel->setData($postData['website']);
            }
            $fieldset = $form->addFieldset('website_fieldset', array(
                'legend' => Mage::helper('core')->__('Website Information')
            ));
            /* @var $fieldset Varien_Data_Form */

            $fieldset->addField('website_name', 'text', array(
                'name'      => 'website[name]',
                'label'     => Mage::helper('core')->__('Website Name'),
                'class'     => 'required-entry',
                'value'     => $websiteModel->getName(),
                'required'  => true
            ));

            $args = array(
                'name'      => 'website[code]',
                'label'     => Mage::helper('core')->__('Website Code'),
                'class'     => 'required-entry',
                'value'     => $websiteModel->getCode(),
                'required'  => true
            );
            if ($websiteModel->getCode() == 'base') {
                $args['readonly'] = 'readonly';
            }
            $fieldset->addField('website_code', 'text', $args);

            $fieldset->addField('website_is_active', 'select', array(
                'name'      => 'website[is_active]',
                'label'     => Mage::helper('core')->__('Status'),
                'class'     => 'required-entry',
                'value'     => $websiteModel->getIsActive(),
                'options'   => array(
                    0 => Mage::helper('adminhtml')->__('Disabled'),
                    1 => Mage::helper('adminhtml')->__('Enabled')),
                'required'  => true
            ));

            $fieldset->addField('website_sort_order', 'text', array(
                'name'      => 'website[sort_order]',
                'label'     => Mage::helper('core')->__('Sort order'),
                'class'     => 'label',
                'value'     => $websiteModel->getSortOrder(),
                'required'  => false
            ));

            $fieldset->addField('website_website_id', 'hidden', array(
                'name'  => 'website[website_id]',
                'value' => $websiteModel->getId()
            ));
        }

        if ($showGroupFieldset) {
            if ($postData = Mage::registry('storefront_post_data')) {
                $groupModel->setData($postData['group']);
            }
            $fieldset = $form->addFieldset('group_fieldset', array(
                'legend' => Mage::helper('core')->__('Store Group Information')
            ));

            if (Mage::registry('storefront_action') == 'edit'
                || Mage::registry('storefront_action') == 'add' && Mage::registry('storefront_type') == 'group') {
                $websites = Mage::getModel('core/website')->getCollection()->toOptionArray();
                $fieldset->addField('group_website_id', 'select', array(
                    'name'      => 'group[website_id]',
                    'label'     => Mage::helper('core')->__('Website'),
                    'class'     => 'required-entry',
                    'value'     => $groupModel->getWebsiteId(),
                    'values'    => $websites,
                    'required'  => true
                ));
            }

            $fieldset->addField('group_name', 'text', array(
                'name'      => 'group[name]',
                'label'     => Mage::helper('core')->__('Group Name'),
                'class'     => 'required-entry',
                'value'     => $groupModel->getName(),
                'required'  => true
            ));

            $categories = Mage::getModel('adminhtml/system_config_source_category')->toOptionArray();

            $fieldset->addField('group_root_category_id', 'select', array(
                'name'      => 'group[root_category_id]',
                'label'     => Mage::helper('core')->__('Root Category'),
                'class'     => 'required-entry',
                'value'     => $groupModel->getRootCategoryId(),
                'values'    => $categories,
                'required'  => true
            ));

            $fieldset->addField('group_group_id', 'hidden', array(
                'name'  => 'group[group_id]',
                'value' => $groupModel->getId()
            ));
        }

        if ($showStoreFieldset) {
            if ($postData = Mage::registry('storefront_post_data')) {
                $storeModel->setData($postData['store']);
            }
            $fieldset = $form->addFieldset('store_fieldset', array(
                'legend' => Mage::helper('core')->__('Store Information')
            ));

            if (Mage::registry('storefront_action') == 'edit'
                || Mage::registry('storefront_action') == 'add' && Mage::registry('storefront_type') == 'store') {
                $groups = Mage::getModel('core/store_group')->getCollection()->toOptionArray();
                $fieldset->addField('store_group_id', 'select', array(
                    'name'      => 'store[group_id]',
                    'label'     => Mage::helper('core')->__('Store group'),
                    'class'     => 'required-entry',
                    'value'     => $storeModel->getGroupId(),
                    'values'    => $groups,
                    'required'  => true
                ));
            }

            $fieldset->addField('store_name', 'text', array(
                'name'      => 'store[name]',
                'label'     => Mage::helper('core')->__('Store Name'),
                'class'     => 'required-entry',
                'value'     => $storeModel->getName(),
                'required'  => true
            ));
            $args = array(
                'name'      => 'store[code]',
                'label'     => Mage::helper('core')->__('Store Code'),
                'class'     => 'required-entry',
                'value'     => $storeModel->getCode(),
                'required'  => true
            );
            if ($storeModel->getCode() == 'base') {
                $args['readonly'] = 'readonly';
            }
            $fieldset->addField('store_code', 'text', $args);

            $fieldset->addField('store_is_active', 'select', array(
                'name'      => 'store[is_active]',
                'label'     => Mage::helper('core')->__('Status'),
                'class'     => 'required-entry',
                'value'     => $storeModel->getIsActive(),
                'options'   => array(
                    0 => Mage::helper('adminhtml')->__('Disabled'),
                    1 => Mage::helper('adminhtml')->__('Enabled')),
                'required'  => true
            ));

            $fieldset->addField('store_sort_order', 'text', array(
                'name'      => 'store[sort_order]',
                'label'     => Mage::helper('core')->__('Sort order'),
                'class'     => 'label',
                'value'     => $storeModel->getSortOrder(),
                'required'  => false
            ));

            $fieldset->addField('store_store_id', 'hidden', array(
                'name'  => 'store[store_id]',
                'value' => $storeModel->getId()
            ));
        }

        $form->addField('storefront_type', 'hidden', array(
            'name'      => 'storefront_type',
            'no_span'   => true,
            'value'     => Mage::registry('storefront_type')
        ));

        $form->addField('storefront_action', 'hidden', array(
            'name'      => 'storefront_action',
            'no_span'   => true,
            'value'     => Mage::registry('storefront_action')
        ));

        $form->setAction(Mage::getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}