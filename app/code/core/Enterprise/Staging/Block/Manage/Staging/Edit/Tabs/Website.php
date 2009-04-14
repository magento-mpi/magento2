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

        $this->setFieldNameSuffix('staging[websites]');

        $this->helper = Mage::helper('enterprise_staging');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Website
     */
    protected function _prepareForm()
    {
        $form          = new Varien_Data_Form();

        $staging       = $this->getStaging();
        $collection    = $staging->getWebsitesCollection();

        $outputFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset = $form->addFieldset('general_fieldset', array('legend'=>Mage::helper('enterprise_staging')->__('Main Information')));

        $fieldset->addField('name', 'text', array(
            'label'     => $this->helper->__('Label'),
            'title'     => $this->helper->__('Label'),
            'name'      => 'name',
            'value'     => $this->getStaging()->getName(),
            'require'   => true
        ));

        $fieldset->addField('code', 'hidden', array(
            'label'     => $this->helper->__('Staging code'),
            'title'     => $this->helper->__('Staging code'),
            'name'      => 'code',
            'value'     => $this->getStaging()->getCode(),
            'require'   => true,
            'note'      => $this->helper->__('Using for staging rollback functionality')
        ));

        foreach ($this->getWebsiteCollection() as $website) {

            $_id = $website->getId();

            $stagingWebsite = $collection->getItemByMasterCode($website->getCode());

            if ($stagingWebsite) {
                $stagingWebsiteName = $stagingWebsite->getName();
            } else {
                $stagingWebsiteName = $website->getName();
            }
            $fieldset = $form->addFieldset('website_fieldset_'.$_id, array('legend'=>$this->helper->__('Website: ') . $stagingWebsiteName));

            $fieldset->addField('master_code_label_'.$_id, 'label',
                array(
                    'label' => $this->helper->__('Master Website Code'),
                    'value' => $website->getCode()
                )
            );

            $fieldset->addField('master_id_'.$_id, 'hidden',
                array(
                    'label' => $this->helper->__('Master Website Id'),
                    'name'  => "{$website->getId()}[master_website_id]",
                    'value' => $website->getId()
                )
            );

            $fieldset->addField('master_code_'.$_id, 'hidden',
                array(
                    'label' => $this->helper->__('Master Website Code'),
                    'name'  => "{$_id}[master_website_code]",
                    'value' => $website->getCode()
                )
            );

            if ($stagingWebsite) {
            	$fieldset->addField('code_'.$_id, 'label',
                    array(
                        'label' => $this->helper->__('Staging Website Code'),
                        'name'  => "{$_id}[code]",
                        'value' => $stagingWebsite->getCode()
                    )
                );

                $fieldset->addField('staging_website_name_'.$_id, 'label',
                    array(
                        'label' => $this->helper->__('Staging Website Name'),
                        'name'  => "{$_id}[name]",
                        'value' => $stagingWebsite->getName()
                    )
                );

                $fieldset->addField('staging_website_base_url_'.$_id, 'label',
                    array(
                        'label' => $this->helper->__('Base Url'),
                        'name'  => "{$_id}[base_url]",
                        'value' => $stagingWebsite->getBaseUrl()
                    )
                );

                $fieldset->addField('staging_website_base_secure_url_'.$_id, 'label',
                    array(
                        'label' => $this->helper->__('Secure Base Url'),
                        'name'  => "{$_id}[base_secure_url]",
                        'value' => $stagingWebsite->getBaseSecureUrl()
                    )
                );

                $fieldset->addField('staging_website_id_'.$_id, 'hidden',
                    array(
                        'label' => $this->helper->__('Staging Website Id'),
                        'name'  => "{$_id}[staging_website_id]",
                        'value' => $stagingWebsite->getId()
                    )
                );

                foreach ($stagingWebsite->getDatasetItemIds() as $usedDatasetItemId) {
                    $fieldset->addField("staging_website_used_dataset_item_id_{$_id}_{$usedDatasetItemId}", 'hidden',
                        array(
                            'label' => $this->helper->__('Staging Website Item Id'),
                            'name'  => "{$_id}[items][{$usedDatasetItemId}][used_dataset_item_id]",
                            'value' => $usedDatasetItemId
                        )
                    );
                }

            } else {
            	$fieldset->addField('staging_website_code_'.$_id, 'text',
	                array(
	                    'label' => $this->helper->__('Staging Website Code'),
	                    'name'  => "{$_id}[code]",
	                    'value' => Mage::getResourceSingleton('enterprise_staging/staging_website')->generateWebsiteCode($website->getCode())
	                )
	            );

	            $fieldset->addField('name_'.$_id, 'text',
                    array(
                        'label' => $this->helper->__('Staging Website Name'),
                        'name'  => "{$_id}[name]",
                        'value' => $website->getName() . $this->helper->__(' (Staging Copy)')
                    )
                );

                if (!Mage::getSingleton('enterprise_staging/entry')->isAutomatic()) {
                    $fieldset->addField('staging_website_base_url_'.$_id, 'text',
                        array(
                            'label' => $this->helper->__('Base Url'),
                            'name'  => "{$_id}[base_url]",
                            'value' => '',
                            'required' => true
                        )
                    );

                    $fieldset->addField('staging_website_base_secure_url_'.$_id, 'text',
                        array(
                            'label' => $this->helper->__('Secure Base Url'),
                            'name'  => "{$_id}[base_secure_url]",
                            'value' => '',
                            'required' => true
                        )
                    );
                }
            }

            $fieldset->addField('visibility_'.$_id, 'select', array(
	            'label'     => $this->helper->__('Frontend restriction'),
	            'title'     => $this->helper->__('Frontend restriction'),
	            'name'      => "{$_id}[visibility]",
	            'value'     => Enterprise_Staging_Model_Staging_Config::VISIBILITY_REQUIRE_HTTP_AUTH,
	            'options'   => Enterprise_Staging_Model_Staging_Config::getOptionArray('visibility')
	        ));

	        $fieldset->addField('master_login_'.$_id, 'text',
	            array(
	                'label'    => $this->helper->__('HTTP Login'),
	                'class'    => 'input-text required-entry validate-login',
	                'name'     => "{$_id}[master_login]",
	                'required' => true
	            )
	        );

	        $fieldset->addField('master_password_'.$_id, 'text',
	            array(
	                'label'    => $this->helper->__('HTTP Password'),
	                'class'    => 'input-text required-entry validate-password',
	                'name'     => "{$_id}[master_password]",
	                'required' => true
	            )
	        );

	        if ($stagingWebsite) {
                foreach ($stagingWebsite->getData() as $key => $value) {
                    $values[$key.'_'.$_id] = $value;
                }

                $form->addValues($values);
	        }

	        $form = $this->_initWebsiteItems($form , $staging, $_id, $stagingWebsite);


            $form = $this->_initWebsiteStore($form , $website, $stagingWebsite);
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
     * @param StagingWebsiteObject $stagingWebsite
     * @return Varien_Data_Form
     */
    protected function _initWebsiteItems($form, $staging, $website_id, $stagingWebsite = null)
    {
        if (empty($staging)) {
            return $form;
        }

        if ($stagingWebsite) {
            $fieldset = $form->addFieldset('staging_website_items', array('legend'=>Mage::helper('enterprise_staging')->__('Items copied')));
        } else {
            $fieldset = $form->addFieldset('staging_website_items', array('legend'=>Mage::helper('enterprise_staging')->__('Select items to be copied')));
        }

        foreach ($staging->getDatasetItemsCollection(true) as $datasetItem) {
            $_id = $datasetItem->getId();

            if ($stagingWebsite) {
                $item_id = $stagingWebsite->getDatasetItemIds();
                if (in_array($datasetItem->getId() , $item_id)) {
                    $fieldset = $this->_initWebsiteItemsStored($fieldset, $datasetItem, $_id);
                }
            } else {
                $fieldset = $this->_initWebsiteItemsNew($fieldset, $datasetItem, $website_id, $_id);
            }
        }

        if (!$stagingWebsite) {
            $fieldset->addField('staging_website_item_check' , 'hidden' ,
                array(
                    'lable'     => 'staging_website_item_check',
                    'name'      => 'staging_website_item_check',
                    'value'     => 'check',
                    'class'     => 'staging_website_item_check'
                )
            );
        }

        return $form;
    }

    /**
     * Init Website Item New Elements
     *
     * @param Varien_Data_Form $fieldset
     * @param Item Object $dataSet
     * @param int $website_id
     * @param string $_id
     * @return Varien_Data_Form
     */
    protected function _initWebsiteItemsNew($fieldset, $dataSet, $website_id, $_id)
    {
        $fieldset->addField('staging_website_items_'.$_id, 'checkbox',
            array(
                'label'    => $dataSet->getName(),
                'name'     => "{$website_id}[dataset_items][]",
                'value'    => $dataSet->getId(),
                'checked'  => true,
            )
        );

        return $fieldset;
    }

    /**
     * Init Website Item Stores Elements
     *
     * @param Varien_Data_Form $fieldset
     * @param Item Object $dataSet
     * @param string $_id
     * @return Varien_Data_Form
     */
    protected function _initWebsiteItemsStored($fieldset, $dataSet, $_id)
    {
        $fieldset->addField('staging_website_items_'.$_id, 'label',
            array(
                'label'    => $dataSet->getName(),
                'name'     => "{$dataSet->getId()}[dataset_items]",
                'value'    => '',
                'checked'  => true,
                'disabled' => true
            )
        );

        return $fieldset;
    }

    /**
     * Init Website Store Elements
     *
     * @param Varien_Data_Form $form
     * @param WebsiteObject $website
     * @param StagingWebsiteObject $stagingWebsite
     * @return Varien_Data_Form
     */
    protected function _initWebsiteStore($form, $website, $stagingWebsite = null)
    {
        if (empty($website)) {
            return $form;
        }

        if ($stagingWebsite) {
            $fieldset = $form->addFieldset('staging_website_stores', array('legend'=>Mage::helper('enterprise_staging')->__('Store views copied')));
        } else {
            $fieldset = $form->addFieldset('staging_website_stores', array('legend'=>Mage::helper('enterprise_staging')->__('Select store views to be copied')));
        }

        if ($stagingWebsite) {
            $_storeCollection = $stagingWebsite->getStoresCollection();
        } else {
            $_storeCollection = $website->getStoreCollection(true);
        }

        if ($_storeCollection) {
            foreach($_storeCollection as $storeView){
                $_id    = $storeView->getId() . '_' . $website->getId();
                if ($stagingWebsite) {
                    $fieldset = $this->_initWebsiteStoreStored($fieldset, $storeView , $_id );
                } else {
                    $fieldset = $this->_initWebsiteStoreNew($fieldset, $storeView , $website->getId(), $_id );
                }
            }
        }
        if (!$stagingWebsite) {
            $fieldset->addField('staging_website_stores_check' , 'hidden' ,
                array(
                    'lable'     => 'staging_website_stores_check',
                    'name'      => 'staging_website_stores_check',
                    'value'     => 'check',
                    'class'     => 'staging_website_stores_check'
                )
            );
        }

        return $form;
    }

    /**
     * Init Existens Website Store Element
     *
     * @param Varien_Data_Form $fieldset
     * @param StoreCollection $storeView
     * @param string $_id
     * @return Varien_Data_Form
     */
    protected function _initWebsiteStoreStored($fieldset, $storeView, $_id)
    {
        $fieldset->addField('staging_store_'.$_id, 'label',
            array(
                'label'    => $storeView->getName(),
                'name'     => "{$_id}[staging_store_id]",
                'value'    => '',
            )
        );
        return $fieldset;
    }

    /**
     * Init Website Store New Element
     *
     * @param Varien_Data_Form $fieldset
     * @param StoreCollection $storeView
     * @param int $websiteId
     * @param string $_id
     * @return Varien_Data_Form
     */
    protected function _initWebsiteStoreNew($fieldset, $storeView, $websiteId, $_id)
    {
        $fieldset->addField('staging_store_'.$_id, 'checkbox',
            array(
                'label'    => $storeView->getName(),
                'name'     => "{$websiteId}[stores][{$websiteId}][{$_id}][staging_store]",
                'value'    => $storeView->getId(),
                'checked'  => true
            )
        );

        $fieldset->addField('master_id_'.$_id, 'hidden',
            array(
                'label' => $this->helper->__('Master Store Id'),
                'name'  => "{$websiteId}[stores][{$websiteId}][{$_id}][master_store_id]",
                'value' => $storeView->getId(),
            )
        );

        $fieldset->addField('master_code_'.$_id, 'hidden',
            array(
                'label' => $this->helper->__('Master Store Code'),
                'name'  => "{$websiteId}[stores][{$websiteId}][{$_id}][master_store_code]",
                'value' => $storeView->getCode()
            )
        );

        $fieldset->addField('code_'.$_id, 'hidden',
            array(
                'label' => $this->helper->__('Staging Store Code'),
                'name'  => "{$websiteId}[stores][{$websiteId}][{$_id}][code]",
                'value' => Mage::getResourceSingleton('enterprise_staging/staging_store')->generateStoreCode($storeView->getCode())
            )
        );

        $fieldset->addField('name_'.$_id, 'hidden',
            array(
                'label' => $this->helper->__('Staging Store Name'),
                'name'  => "{$websiteId}[stores][{$websiteId}][{$_id}][name]",
                'value' => $storeView->getName()
            )
        );
        return $fieldset;

    }

    /**
     * return website collection
     *
     * @return object
     */
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

    /**
     * return store group collection
     *
     * @return object
     */
    public function getGroupCollection($website)
    {
        if (!$website instanceof Mage_Core_Model_Website) {
            $website = Mage::getModel('core/website')->load($website);
        }
        return $website->getGroupCollection();
    }

    /**
     * return store collection
     *
     * @return object
     */
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
     * return website name
     *
     * @param Enterprise_Staging_Model_Staging_Website $website
     * @return string
     */
    public function getWebsiteName($website)
    {
        $name = $website->getName() . '&nbsp;' . $website->getCode();
        if ($website->getIsStaging()) {
            $name .= '&nbsp;is&nbsp;staging';
        }
        return $name;
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
