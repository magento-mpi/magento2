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
 * Staging rollback settings of staging website type block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Rollback_Settings_Website extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('enterprise/staging/manage/staging/rollback/settings/website.phtml');
        $this->setId('staging_website_mapper');
        $this->setUseAjax(true);

        $this->setRowInitCallback($this->getJsObjectName().'.stagingWebsiteMapperRowInit');
        $this->setIsReadyForRollback(true);
    }

    protected function _prepareLayout()
    {
        $this->setChild('rollback_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'   => Mage::helper('enterprise_staging')->__('Rollback'),
                    'onclick' => $this->getJsObjectName().'.submit()',
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
     * Retrieve currently edited backup object
     *
     * @return Enterprise_Staging_Model_Staging_Backup
     */
    public function getBackup()
    {
        if (!($this->getData('staging_backup') instanceof Enterprise_Staging_Model_Staging_Backup)) {
            $this->setData('staging_backup', Mage::registry('staging_backup'));
        }
        return $this->getData('staging_backup');
    }
        
    /**
     * Retrieve event 
     *
     * @return Enterprise_Staging_Block_Manage_Staging-Event
     */
    public function getEvent()
    {
        if (!($this->getData('staging_event') instanceof Enterprise_Staging_Model_Staging_Event)) {
            $this->setData('staging_event', Mage::registry('staging_event'));
        }
        return $this->getData('staging_event');
    }
    
    /**
     * Retrieve staging object of current event 
     *
     * @return Enterprise_Staging_Block_Manage_Staging
     */
    public function getStaging()
    {
        return $this->getEvent()->getStaging();
    }
    
    public function getMapper()
    {
        return $this->getStaging()->getMapperInstance();
    }
    
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/rollbackPost', array('_current'=>true, 'back'=>null));
    }

    public function getWebsiteCollection()
    {
        $collection = Mage::getModel('core/website')->getResourceCollection();

        $staging = $this->getStaging();

        //$collection->addFieldToFilter('is_staging',array('neq'=>1));

        return $collection->load();
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
        $html = '';
        //$html = parent::getMainButtonsHtml();
        if($this->getIsReadyForRollback()){
            $html.= $this->getChildHtml('rollback_button');
        }
        return $html;
    }
}