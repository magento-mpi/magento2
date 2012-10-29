<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Staging rollback settings of staging website type block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Backup_Edit_Tabs_Rollback extends Mage_Adminhtml_Block_Widget_Form
{

    protected $_template = 'backup/rollback.phtml';

    protected function _construct()
    {
        parent::_construct();

        $this->setId('staging_website_mapper');
        $this->setUseAjax(true);

        $this->setRollbackJsObjectName('enterpriseRollbackForm');
        $this->setRowInitCallback($this->getRollbackJsObjectName().'.stagingWebsiteMapperRowInit');
        $this->setIsReadyForRollback(true);
    }

    /**
     * Prepare layout
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareLayout()
    {
        $this->setChild('website_store_form',
            $this->getLayout()
                ->createBlock('Enterprise_Staging_Block_Adminhtml_Backup_Edit_Tabs_Rollback_Store')
        );

        $this->setChild('rollbackGrid',
            $this->getLayout()->createBlock(
                'Enterprise_Staging_Block_Adminhtml_Backup_Edit_Tabs_Rollback_Items',
                'staging.rollback.items'
            )->setExtendInfo($this->getBackup()->getItemVersionCheck()));

        return parent::_prepareLayout();
    }

    /**
     * Retrieve currently edited backup object
     *
     * @return Enterprise_Staging_Model_Staging_Backup
     */
    public function getBackup()
    {
        if (!($this->getData('staging_backup') instanceof Enterprise_Staging_Model_Staging_Action)) {
            $this->setData('staging_backup', Mage::registry('staging_backup'));
        }
        return $this->getData('staging_backup');
    }

    /**
     * Retrieve staging object of current backup
     *
     * @return Enterprise_Staging_Block_Manage_Staging
     */
    public function getStaging()
    {
        if (!($this->getData('staging') instanceof Enterprise_Staging_Model_Staging)) {
            $this->setData('staging', $this->getBackup()->getStaging());
        }
        return $this->getData('staging');
    }

    /**
     * Return mapper instance
     */
    public function getMapper()
    {
        return $this->getStaging()->getMapperInstance();
    }

    /**
     * Return Save url
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/rollbackPost', array('_current'=>true));
    }

    /**
     * Return website collection
     *
     * @return object
     */
    public function getWebsiteCollection()
    {
        return Mage::app()->getWebsites();
    }

    /**
     * return store collection
     *
     * @return object
     */
    public function getAllStoresCollection()
    {
        return Mage::app()->getStores();
    }

    /**
     * return stores as json
     *
     * @return string
     */
    public function getAllStoresJson()
    {
        $stores = array();
        foreach ($this->getAllStoresCollection() as $store) {
            $stores[$store->getWebsiteId()][] = $store->getData();
        }
        if (!$stores) {
            return '{}';
        } else {
            return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($stores);
        }
    }

    /**
     * return main buttons as html structure
     *
     * @return string
     */
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
