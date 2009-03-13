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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Staging entities tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Website extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        //$this->setTemplate('enterprise/staging/manage/staging/edit/tabs/website.phtml');
        //$this->setUseConfirm(true);
        //$this->setUseAjax(true);
        //$this->setDefaultStoreName($this->__('All Stores & Store Views'));
    }

    /**
     * Retrive product object from object if not from registry
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

    protected function _prepareForm()
    {
        $form          = new Varien_Data_Form();

        $staging       = $this->getStaging();
        $collection    = $staging->getWebsitesCollection();

        $outputFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        foreach ($this->getWebsiteCollection() as $website) {
            $_id = $website->getId();
            $stagingWebsite = $collection->getItemByMasterCode($website->getCode());

            $fieldset = $form->addFieldset('website_fieldset_'.$_id, array('legend'=>Mage::helper('enterprise_staging')->__($website->getName())));

            $fieldset->addField('master_id_label_'.$_id, 'label',
                array(
                    'label'    => Mage::helper('enterprise_staging')->__('Master Website Id'),
                    'value'    => $website->getId()
                )
            );

            $fieldset->addField('master_code_label_'.$_id, 'label',
                array(
                    'label'    => Mage::helper('enterprise_staging')->__('Master Website Code'),
                    'value'    => $website->getCode()
                )
            );

            $fieldset->addField('master_id_'.$_id, 'hidden',
                array(
                    'label'    => Mage::helper('enterprise_staging')->__('Master Website Id'),
                    'name'     => "{$website->getId()}[master_website_id]",
                    'value'    => $website->getId()
                )
            );

            $fieldset->addField('master_code_'.$_id, 'hidden',
                array(
                    'label'    => Mage::helper('enterprise_staging')->__('Master Website Code'),
                    'name'     => "{$website->getId()}[master_website_code]",
                    'value'    => $website->getCode()
                )
            );

            if ($stagingWebsite) {
            	$fieldset->addField('code_'.$_id, 'text',
                    array(
                        'label'    => Mage::helper('enterprise_staging')->__('Staging Website Code'),
                        'name'     => "{$website->getId()}[code]",
                        'value'    => $stagingWebsite->getCode()
                    )
                );

                $fieldset->addField('name_'.$_id, 'text',
                    array(
                        'label'    => Mage::helper('enterprise_staging')->__('Staging Website Name'),
                        'name'     => "{$website->getId()}[name]",
                        'value'    => $stagingWebsite->getName()
                    )
                );
            } else {
            	$fieldset->addField('code_'.$_id, 'text',
	                array(
	                    'label'    => Mage::helper('enterprise_staging')->__('Staging Website Code'),
	                    'name'     => "{$website->getId()}[code]",
	                    'value'    => Mage::getResourceSingleton('enterprise_staging/staging_website')->generateWebsiteCode($website->getCode())
	                )
	            );

	            $fieldset->addField('name_'.$_id, 'text',
                    array(
                        'label'    => Mage::helper('enterprise_staging')->__('Staging Website Name'),
                        'name'     => "{$website->getId()}[name]",
                        'value'    => $website->getName()
                    )
                );
            }

            $fieldset->addField('visibility_'.$_id, 'select', array(
	            'label'     => Mage::helper('enterprise_staging')->__('Frontend Visibility'),
	            'title'     => Mage::helper('enterprise_staging')->__('Frontend Visibility'),
	            'name'      => "{$website->getId()}[visibility]",
	            'value'     => Enterprise_Staging_Model_Config::VISIBILITY_WHILE_MASTER_LOGIN,
	            'options'   => Enterprise_Staging_Model_Config::getOptionArray()
	        ));

	        $fieldset->addField('master_login_'.$_id, 'text',
	            array(
	                'label' => Mage::helper('enterprise_staging')->__('Master Login'),
	                'class' => 'input-text required-entry validate-login',
	                'name'  => "{$website->getId()}[master_login]",
	                'required' => true
	            )
	        );

	        $fieldset->addField('master_password_'.$_id, 'text',
	            array(
	                'label' => Mage::helper('enterprise_staging')->__('Master Password'),
	                'class' => 'input-text required-entry validate-password',
	                'name'  => "{$website->getId()}[master_password]",
	                'required' => true
	            )
	        );





            $fieldset->addField('auto_apply_is_active_'.$_id, 'select', array(
                'label' => Mage::helper('enterprise_staging')->__('Auto Apply Is Active'),
                'title' => Mage::helper('enterprise_staging')->__('Auto Apply Is Active'),
                'name'  => "{$website->getId()}[auto_apply_is_active]",
                'options' => Mage::getSingleton('eav/entity_attribute_source_boolean')->getOptionArray(),
            ));

            $fieldset->addField('apply_date_'.$_id, 'date', array(
                'label' => Mage::helper('enterprise_staging')->__('Auto Apply Date'),
                'title' => Mage::helper('enterprise_staging')->__('Auto Apply Date'),
                'name'  => "{$website->getId()}[apply_date]",
                'time'  => false,
                'format'=> $outputFormat,
                'image' => $this->getSkinUrl('images/grid-cal.gif')
            ));




            $fieldset->addField('auto_rollback_is_active_'.$_id, 'select', array(
                'label' => Mage::helper('enterprise_staging')->__('Auto Rollback Is Active'),
                'title' => Mage::helper('enterprise_staging')->__('Auto Rollback Is Active'),
                'name'  => "{$website->getId()}[auto_rollback_is_active]",
                'options' => Mage::getSingleton('eav/entity_attribute_source_boolean')->getOptionArray(),
            ));

            $fieldset->addField('rollback_date_'.$_id, 'date', array(
                'label' => Mage::helper('enterprise_staging')->__('Auto Rollback Date'),
                'title' => Mage::helper('enterprise_staging')->__('Auto Rollback Date'),
                'name'  => "{$website->getId()}[rollback_date]",
                'time'  => false,
                'format'=> $outputFormat,
                'image' => $this->getSkinUrl('images/grid-cal.gif')
            ));

            if ($stagingWebsite) {
                $values = array();
                foreach ($stagingWebsite->getData() as $key => $value) {
                    $values[$key.'_'.$_id] = $value;
                }
                $form->addValues($values);
            }
        }

        $form->setFieldNameSuffix('staging[websites]');
        $this->setForm($form);
    }

    public function getWebsiteCollection()
    {
        $collection = Mage::getModel('core/website')->getResourceCollection();

        $staging = $this->getStaging();

        $websiteIds = $staging->getMasterWebsiteIds();
        if (!is_null($websiteIds)) {
            $collection->addIdFilter($websiteIds);
        }

        $collection->addFieldToFilter('is_staging',array('neq'=>1));

        return $collection->load();
    }

    public function getGroupCollection($website)
    {
        if (!$website instanceof Mage_Core_Model_Website) {
            $website = Mage::getModel('core/website')->load($website);
        }
        return $website->getGroupCollection();
    }

    public function getStoreCollection($group)
    {
        if (!$group instanceof Mage_Core_Model_Store_Group) {
            $group = Mage::getModel('core/store_group')->load($group);
        }
        $stores = $group->getStoreCollection();
        if (!empty($this->_storeIds)) {
            $stores->addIdFilter($this->getStoreIds());
        }
        return $stores;
    }

    /**
     * TODO need to remove if unneeded
     */
    public function getGroupIds()
    {
        if ($this->getStaging()->getId()) {
            return array();
        } else {
            return array();
        }
    }

    public function getStoreId()
    {
        return $this->getRequest()->getParam('store');
    }

    public function setStoreIds($storeIds)
    {
        $this->_storeIds = $storeIds;
        return $this;
    }

    public function getWebsiteName($website)
    {
        $name = $website->getName() . '&nbsp;' . $website->getCode();
        if ($website->getIsStaging()) {
            $name .= '&nbsp;staging';
        }
        return $name;
    }

    public function getStoreIds()
    {
        return $this->_storeIds;
    }
}