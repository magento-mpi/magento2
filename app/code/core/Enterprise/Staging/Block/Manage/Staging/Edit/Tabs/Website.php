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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Staging entities tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Website extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Keep main translate helper instance
     *
     * @var object Mage_Core_Helper_Abstract
     */
    protected $helper;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setFieldNameSuffix('staging');

        $this->helper = Mage::helper('enterprise_staging');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Website
     */
    protected function _prepareForm()
    {
        $form       = new Varien_Data_Form();
        $staging    = $this->getStaging();
        $fieldset   = $form->addFieldset('general_fieldset',
            array('legend' => Mage::helper('enterprise_staging')
                ->__('General Information')));

        $fieldset->addField('name', 'text', array(
            'label'     => $this->helper->__('Label'),
            'title'     => $this->helper->__('Label'),
            'name'      => 'name',
            'value'     => $this->getStaging()->getName(),
            'require'   => true
        ));

        $masterWebsite = $staging->getMasterWebsite();
        if ($masterWebsite) {
            $_id = $masterWebsite->getId();

            $stagingWebsite = $staging->getStagingWebsite();
            if ($stagingWebsite) {
                $stagingWebsiteName = $stagingWebsite->getName();
            } else {
                $stagingWebsiteName = $masterWebsite->getName();
            }

            $fieldset = $form->addFieldset('website_fieldset_'.$_id,
                array('legend' => $this->helper->__('Staging Website')));

            $fieldset->addField('master_website_code_label_'.$_id, 'label',
                array(
                    'label' => $this->helper->__('Master Website Code'),
                    'value' => $masterWebsite->getCode()
                )
            );

            $fieldset->addField('master_website_id_'.$_id, 'hidden',
                array(
                    'label' => $this->helper->__('Master Website Id'),
                    'name'  => "websites[{$_id}][master_website_id]",
                    'value' => $_id
                )
            );

            $fieldset->addField('master_website_code_'.$_id, 'hidden',
                array(
                    'label' => $this->helper->__('Master Website Code'),
                    'name'  => "websites[{$_id}][master_website_code]",
                    'value' => $masterWebsite->getCode()
                )
            );

            if ($stagingWebsite) {
                $fieldset->addField('staging_website_code_'.$_id, 'label',
                    array(
                        'label' => $this->helper->__('Staging Website Code'),
                        'name'  => "websites[{$_id}][code]",
                        'value' => $stagingWebsite->getCode()
                    )
                );

                $fieldset->addField('staging_website_name_'.$_id, 'label',
                    array(
                        'label' => $this->helper->__('Staging Website Name'),
                        'name'  => "websites[{$_id}][name]",
                        'value' => $stagingWebsite->getName()
                    )
                );

                $element = $fieldset->addField('staging_website_base_url_'.$_id, 'label',
                    array(
                        'label' => $this->helper->__('Base Url'),
                        'name'  => "websites[{$_id}][base_url]",
                        'value' => $stagingWebsite->getConfig('web/unsecure/base_url')
                    )
                );

                if ($stagingWebsite->getStoresCount() > 0) {
                    $element->setRenderer($this->getLayout()->createBlock('enterprise_staging/manage_staging_renderer_link'));
                }

                $element = $fieldset->addField('staging_website_base_secure_url_'.$_id, 'label',
                    array(
                        'label' => $this->helper->__('Secure Base Url'),
                        'name'  => "websites[{$_id}][base_secure_url]",
                        'value' => $stagingWebsite->getConfig('web/secure/base_url')
                    )
                );
                if ($stagingWebsite->getStoresCount() > 0) {
                    $element->setRenderer($this->getLayout()->createBlock('enterprise_staging/manage_staging_renderer_link'));
                }

                $fieldset->addField('staging_website_id_'.$_id, 'hidden',
                    array(
                        'label' => $this->helper->__('Staging Website Id'),
                        'name'  => "websites[{$_id}][staging_website_id]",
                        'value' => $stagingWebsite->getId()
                    )
                );
            } else {
                $fieldset->addField('staging_website_code_'.$_id, 'text',
                    array(
                        'label' => $this->helper->__('Staging Website Code'),
                        'name'  => "websites[{$_id}][code]",
                        'value' => Mage::helper('enterprise_staging/website')->generateWebsiteCode($masterWebsite->getCode())
                    )
                );

                $fieldset->addField('staging_website_name_'.$_id, 'text',
                    array(
                        'label' => $this->helper->__('Staging Website Name'),
                        'name'  => "websites[{$_id}][name]",
                        'value' => $masterWebsite->getName() . $this->helper->__(' (Staging Copy)')
                    )
                );

                if (!Mage::getSingleton('enterprise_staging/entry')->isAutomatic()) {
                    $fieldset->addField('staging_website_base_url_'.$_id, 'text',
                        array(
                            'label' => $this->helper->__('Base Url'),
                            'name'  => "websites[{$_id}][base_url]",
                            'value' => '',
                            'required' => true
                        )
                    );

                    $fieldset->addField('staging_website_base_secure_url_'.$_id, 'text',
                        array(
                            'label' => $this->helper->__('Secure Base Url'),
                            'name'  => "websites[{$_id}][base_secure_url]",
                            'value' => '',
                            'required' => true
                        )
                    );
                }
            }

            $fieldset->addField('staging_website_visibility_'.$_id, 'select', array(
                'label'     => $this->helper->__('Frontend restriction'),
                'title'     => $this->helper->__('Frontend restriction'),
                'name'      => "websites[{$_id}][visibility]",
                'value'     => $stagingWebsite ? $stagingWebsite->getVisibility() : Enterprise_Staging_Model_Staging_Config::VISIBILITY_REQUIRE_HTTP_AUTH,
                'options'   => Enterprise_Staging_Model_Staging_Config::getOptionArray('visibility')
            ));

            $fieldset->addField('staging_website_master_login_'.$_id, 'text',
                array(
                    'label'    => $this->helper->__('HTTP Login'),
                    //'class'    => 'input-text required-entry validate-login',
                    'name'     => "websites[{$_id}][master_login]",
                    'required' => false,
                    'value'    => $stagingWebsite ? $stagingWebsite->getMasterLogin() : ''
                )
            );

            $fieldset->addField('staging_website_master_password_'.$_id, 'text',
                array(
                    'label'    => $this->helper->__('HTTP Password'),
                    //'class'    => 'input-text required-entry validate-password',
                    'name'     => "websites[{$_id}][master_password]",
                    'required' => false,
                    'value'    => $stagingWebsite ? Mage::helper('core')->decrypt($stagingWebsite->getMasterPassword()) : ''
                )
            );

            if ($stagingWebsite) {
                foreach ($stagingWebsite->getData() as $key => $value) {
                    if ($key == 'master_password') {
                        continue;
                    }
                    $values[$key.'_'.$_id] = $value;
                }
                $form->addValues($values);
            }

            $this->_initWebsiteItems($form , $staging, $_id, $stagingWebsite);

            $this->_initWebsiteStore($form , $masterWebsite, $stagingWebsite);
        }

        $form->addFieldNameSuffix($this->getFieldNameSuffix());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Init Website Item Elements
     *
     * @param Varien_Data_Form $form
     * @param Staging Object $staging
     * @param int $website_id
     * @param Mage_Core_Model_Website $stagingWebsite
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Website
     */
    protected function _initWebsiteItems($form, $staging, $websiteId, $stagingWebsite = null)
    {
        if (empty($staging)) {
            return $this;
        }

        if ($stagingWebsite) {
            $fieldset = $form->addFieldset('staging_website_items',
                array('legend'=>Mage::helper('enterprise_staging')
                    ->__('Items copied')));
        } else {
            $fieldset = $form->addFieldset('staging_website_items',
                array('legend' => Mage::helper('enterprise_staging')
                    ->__('Select Original Website Content to be Copied to Staging Website')));
        }

        $usedItemCodes = $staging->getStagingItemCodes();

        foreach (Enterprise_Staging_Model_Staging_Config::getStagingItems()->children() as $stagingItem) {
            if ((int)$stagingItem->is_backend || (int)$stagingItem->is_extend) {
                continue;
            }
            $_code = (string) $stagingItem->code;

            if ($stagingWebsite) {
                if (in_array($_code, $usedItemCodes)) {
                    $this->_initWebsiteItemsStored($fieldset, $stagingItem, $_code);
                }
            } else {
                $this->_initWebsiteItemsNew($fieldset, $stagingItem, $websiteId, $_code);
            }
        }

        if (!$stagingWebsite) {
            $fieldset->addField('staging_website_item_check' , 'hidden' ,
                array(
                    'lable'     => 'Staging Website Item Check',
                    'name'      => 'item_check',
                    'value'     => 'check',
                    'class'     => 'staging_website_item_check'
                )
            );
        }

        return $this;
    }

    /**
     * Init Website Item New Elements
     *
     * @param Varien_Data_Form $fieldset
     * @param Varien_Simplexml_Element $stagingItem
     * @param int $websiteId
     * @param string $_code
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Website
     */
    protected function _initWebsiteItemsNew($fieldset, $stagingItem, $websiteId, $_code)
    {
        $fieldset->addField('staging_website_items_'.$_code, 'checkbox',
            array(
                'label'    => (string) $stagingItem->label,
                'name'     => "staging_items[$_code][staging_item_code]",
                'value'    => $_code,
                'checked'  => true,
            )
        );

        return $this;
    }

    /**
     * Init Website Item Stores Elements
     *
     * @param Varien_Data_Form $fieldset
     * @param Varien_Simplexml_Element $stagingItem
     * @param string $_code
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Website
     */
    protected function _initWebsiteItemsStored($fieldset, $stagingItem, $_code)
    {
        $fieldset->addField('staging_website_items_'.$_code, 'label',
            array(
                'label' => (string) $stagingItem->label
            )
        );

        return $this;
    }

    /**
     * Init Website Store Elements
     *
     * @param Varien_Data_Form $form
     * @param Mage_Core_Model_Website $masterWebsite
     * @param Mage_Core_Model_Website $stagingWebsite
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Website
     */
    protected function _initWebsiteStore($form, $masterWebsite, $stagingWebsite = null)
    {
        if (empty($masterWebsite)) {
            return $this;
        }

        if ($stagingWebsite) {
            $fieldset = $form->addFieldset('staging_website_stores',
                array('legend' => Mage::helper('enterprise_staging')
                    ->__('Store views copied')));
        } else {
            $fieldset = $form->addFieldset('staging_website_stores',
                array('legend' => Mage::helper('enterprise_staging')
                    ->__('Select Original Website Store Views to be Copied to Staging Website')));
        }

        if ($stagingWebsite) {
            $_storeGroups = $stagingWebsite->getGroups();
        } else {
            $_storeGroups = $masterWebsite->getGroups();
        }

        foreach ($_storeGroups as $group) {
            if ($group->getStoresCount()) {
                $_stores = $group->getStores();
                $this->_initStoreGroup($fieldset, $group);
                foreach ($_stores as $storeView) {
                    $this->_initStoreView($fieldset, $storeView);
                }
            } else {
                $fieldset->addField('staging_no_stores', 'label',
                    array(
                        'label' => Mage::helper('enterprise_staging')->__('There are no store views were copied')
                    )
                );
            }
        }

        return $this;
    }

    /**
     * Init Staging Store Group
     *
     * @param Varien_Data_Form $fieldset
     * @param Mage_Core_Model_Store_Group $group
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Website
     */
    protected function _initStoreGroup($fieldset, $group)
    {
        $fieldset->addField('staging_store_group_' . $group->getId(), 'label',
            array(
                'label' => $group->getName()
            )
        );

        return $this;
    }

    /**
     * Init Staging Store Views
     *
     * @param Varien_Data_Form $fieldset
     * @param Mage_Core_Model_Store $storeView
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Website
     */
    protected function _initStoreView($fieldset, $storeView)
    {
        $_shift = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        if (!$storeView->getWebsite()->getIsStaging()) {
            $_id        = $storeView->getId();
            $websiteId  = $storeView->getWebsiteId();

            $fieldset->addField('master_store_use_'.$_id, 'checkbox',
                array(
                    'label'    => $_shift . $storeView->getName(),
                    'name'     => "websites[{$websiteId}][stores][{$_id}][use]",
                    'value'    => $storeView->getId(),
                    'checked'  => true
                )
            );

            $fieldset->addField('master_store_id_'.$_id, 'hidden',
                array(
                    'label' => $this->helper->__('Master Store Id'),
                    'name'  => "websites[{$websiteId}][stores][{$_id}][master_store_id]",
                    'value' => $storeView->getId(),
                )
            );

            $fieldset->addField('master_store_code_'.$_id, 'hidden',
                array(
                    'label' => $this->helper->__('Master Store Code'),
                    'name'  => "websites[{$websiteId}][stores][{$_id}][master_store_code]",
                    'value' => $storeView->getCode()
                )
            );

            $fieldset->addField('staging_store_code_'.$_id, 'hidden',
                array(
                    'label' => $this->helper->__('Staging Store Code'),
                    'name'  => "websites[{$websiteId}][stores][{$_id}][code]",
                    'value' => Mage::helper('enterprise_staging/store')->generateStoreCode($storeView->getCode())
                )
            );

            $fieldset->addField('staging_store_name_'.$_id, 'hidden',
                array(
                    'label' => $this->helper->__('Staging Store Name'),
                    'name'  => "websites[{$websiteId}][stores][{$_id}][name]",
                    'value' => $storeView->getName()
                )
            );
        } else {
            $fieldset->addField('staging_store_'.$storeView->getId(), 'label',
                array(
                    'label' => $_shift . $storeView->getName()
                )
            );
        }
        return $this;
    }

    /**
     * Retrive staging object from setted data if not from registry
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        if (!($this->getData('staging') instanceof Enterprise_Staging_Model_Staging)) {
            $this->setData('staging', Mage::registry('staging'));
        }
        return $this->getData('staging');
    }
}
