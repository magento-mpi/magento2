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
 * Staging item data
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Website_Store_Item extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Keep main translate helper instance
     *
     * @var object
     */
    protected $helper;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setFieldNameSuffix('staging[stores]');

        $this->helper = Mage::helper('enterprise_staging');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Item
     */
    protected function _prepareForm()
    {
        $form         = new Varien_Data_Form();

        $staging      = $this->getStaging();

        $website      = $this->getWebsite();
        $websiteId    = $website->getId();

        $store        = $this->getStore();
        $storeId          = $store->getId();

        $stagingWebsite = $this->getStagingWebsite();

        $stagingStore = $this->getStagingStore();

        if ($stagingStore) {
            $_id = $store->getId().'-'.$stagingStore->getId();
        } else {
            $counter = $this->getRequest()->getPost('count');
            $_id = $store->getId().'_'.$counter;
        }

        $fieldset = $form->addFieldset("staging_store_{$_id}",
            array(
                'legend' => $this->helper->__($store->getName()),
                'fieldset_container_id' => "fieldset_staging_store_{$_id}"
        ));

        $fieldset->addField('store_remove_'.$_id, 'button',
            array(
                'value'     => $this->helper->__('Remove'),
                'class'     => 'button',
                'onclick'   => "removeStagingStore('fieldset_staging_store_{$_id}')"
            )
        );

        $fieldset->addField('store_id_label_'.$_id, 'label',
            array(
                'label' => $this->helper->__('Master Store Id'),
                'value' => $storeId
            )
        );

        $fieldset->addField('store_code_label_'.$_id, 'label',
            array(
                'label' => $this->helper->__('Master Store Code'),
                'value' => $store->getCode()
            )
        );

        $fieldset->addField('master_id_'.$_id, 'hidden',
            array(
                'label' => $this->helper->__('Master Store Id'),
                'name'  => "{$_id}[master_store_id]",
                'value' => $storeId
            )
        );

        $fieldset->addField('master_code_'.$_id, 'hidden',
            array(
                'label' => $this->helper->__('Master Store Code'),
                'name'  => "{$_id}[master_store_code]",
                'value' => $store->getCode()
            )
        );

        if ($stagingStore) {
            $fieldset->addField("staging_store_id_{$_id}", 'hidden',
                array(
                    'name'  => "{$_id}[staging_store_id]",
                    'value' => $stagingStore->getId()
                )
            );
            $fieldset->addField('staging_store_code_'.$_id, 'text',
                array(
                    'label' => $this->helper->__('Staging Store Code'),
                    'name'  => "{$_id}[code]",
                    'value' => $stagingStore->getCode()
                )
            );

            $fieldset->addField('staging_store_name_'.$_id, 'text',
                array(
                    'label' => $this->helper->__('Staging Store Name'),
                    'name'  => "{$_id}[name]",
                    'value' => $stagingStore->getName()
                )
            );

            foreach ($stagingStore->getDatasetItemIds() as $usedDatasetItemId) {
                $fieldset->addField("staging_store_used_dataset_item_id_{$_id}_{$usedDatasetItemId}", 'hidden',
                    array(
                        'label' => $this->helper->__('Staging Store Item Id'),
                        'name'  => "{$_id}[items][{$usedDatasetItemId}][staging_item_id]",
                        'value' => $usedDatasetItemId
                    )
                );
            }
        } else {
            $fieldset->addField('code_'.$_id, 'text',
                array(
                    'label' => $this->helper->__('Staging Store Code'),
                    'name'  => "{$_id}[code]",
                    'value' => Mage::getResourceSingleton('enterprise_staging/staging_store')->generateStoreCode($store->getCode())
                )
            );

            $fieldset->addField('name_'.$_id, 'text',
                array(
                    'label' => $this->helper->__('Staging Store Name'),
                    'name'  => "{$_id}[name]",
                    'value' => $store->getName()
                )
            );
        }

        $fieldset->addField("staging_store_items_toggle_{$_id}", 'checkbox',
            array(
                'label'   => $this->helper->__('Use specific Items'),
                'name'    => "{$_id}[use_specific_items]",
                'value'   => "1",
                'onclick' => "toggleStagingStoreItems('staging_store_items_{$_id}')",
                'checked' => ($stagingStore && $stagingStore->getUseSpecificItems())
            )
        );

        $params = array(
            'label'     => $this->helper->__('Copy to Staging Store'),
            'name'      => "{$_id}[dataset_items]",
            'value'     => $stagingStore ? $stagingStore->getDatasetItemIds() : array(),
            'values'    => $staging->getDatasetItemsCollection(true)->toOptionArray()
        );
        if (!$stagingStore || !$stagingStore->getUseSpecificItems()) {
            $params['disabled'] = true;
        }
        $fieldset->addField("staging_store_items_{$_id}", 'multiselect', $params);

        if ($stagingStore) {
            $values = array();
            foreach ($stagingStore->getData() as $key => $value) {
                $values[$key.'_'.$_id] = $value;
            }
            $form->addValues($values);
        }

        $form->setFieldNameSuffix($this->getFieldNameSuffix()."[{$websiteId}]");
        $this->setForm($form);

        return parent::_prepareForm();
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

    /**
     * Retrive website object from setted data if not from registry
     *
     * @return Mage_Core_Model_Website
     */
    public function getWebsite()
    {
        if (!($this->getData('website') instanceof Mage_Core_Model_Website)) {
            $websiteId = (int) $this->getRequest()->getParam('website');
            $website = Mage::app()->getWebsite($websiteId);
            $this->setData('website', $website);
        }
        return $this->getData('website');
    }

    /**
     * Retrive store object from setted data if not from param
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (!($this->getData('store') instanceof Mage_Core_Model_Store)) {
            $storeId = (int) $this->getRequest()->getPost('store');
            $store = Mage::app()->getStore($storeId);
            $this->setData('store', $store);
        }
        return $this->getData('store');
    }

    /**
     * Retrive staging website object from setted data if not from param
     *
     * @return Mage_Core_Model_Store
     */
    public function getStagingWebsite()
    {
        if (!($this->getData('staging_website') instanceof Enterprise_Staging_Model_Staging_Website)) {
            $stagingWebsiteId = (int) $this->getRequest()->getParam('staging_website');
            $website = false;
            if ($stagingWebsiteId) {
                $website = Mage::getModel('enterprise_staging/staging_website')
                    ->load($stagingWebsiteId);
            }
            $this->setData('staging_website', $website);
        }
        return $this->getData('staging_website');
    }

    /**
     * Retrive staging store object from setted data if not from post
     *
     * @return Mage_Core_Model_Store
     */
    public function getStagingStore()
    {
        if (!($this->getData('staging_store') instanceof Enterprise_Staging_Model_Staging_Store)) {
            $stagingStoreId = (int) $this->getRequest()->getParam('staging_store');
            $store = false;
            if ($stagingStoreId) {
                $store = Mage::getModel('enterprise_staging/staging_store')
                    ->load($stagingStoreId);
            }
            $this->setData('staging_store', $store);
        }
        return $this->getData('staging_store');
    }
}
