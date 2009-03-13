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
 * Staging merge setting staging website type block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Merge_Settings_Website extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('enterprise/staging/manage/staging/merge/settings/website.phtml');
        $this->setId('staging_website_mapper');
        $this->setUseAjax(true);
        $this->setRowInitCallback($this->getJsObjectName().'.stagingWebsiteMapperRowInit');

        $this->setIsReadyForMerge(true);
    }

    protected function _prepareLayout()
    {
        $this->setChild('merge_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'   => Mage::helper('enterprise_staging')->__('Merge'),
                    'onclick' => $this->getJsObjectName().'.stagingMerge()',
                    'class'   => 'task'
                ))
        );

        $this->setChild('items',
            $this->getLayout()
                ->createBlock('enterprise_staging/manage_staging_edit_tabs_item')
                ->setFieldNameSuffix('map[items]')
        );
        return parent::_prepareLayout();
    }

    /**
     * Retrieve currently edited staging object
     *
     * @return Enterprise_Staging_Block_Manage_Staging
     */
    public function getStaging()
    {
        if (!($this->getData('staging') instanceof Enterprise_Staging_Model_Staging)) {
            $this->setData('staging', Mage::registry('staging'));
        }
        return $this->getData('staging');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/mergePost', array('_current'=>true, 'back'=>null));
    }

    public function getWebsiteCollection()
    {
        $collection = Mage::getModel('core/website')->getResourceCollection();

        $staging = $this->getStaging();

        $websiteIds = $staging->getMasterWebsiteIds();
        if (!is_null($websiteIds)) {
            //$collection->addIdFilter($websiteIds);
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

    public function getAllStoresCollection()
    {
        return Mage::app()->getStores();
    }

    public function getAllStoresJson()
    {
        $stores = array();
        foreach ($this->getAllStoresCollection() as $store) {
            $stores[$store->getWebsiteId()][] = $store->getData();
        }
        if (!$stores) {
            return '{}';
        } else {
            return Zend_Json::encode($stores);
        }
    }

    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml();
        if($this->getIsReadyForMerge()){
            $html.= $this->getChildHtml('merge_button');
        }
        return $html;
    }
}